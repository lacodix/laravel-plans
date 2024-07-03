<?php

namespace Lacodix\LaravelPlans\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lacodix\LaravelPlans\Classes\Period;
use Lacodix\LaravelPlans\Contracts\Subscriber;
use Lacodix\LaravelPlans\Enums\Interval;
use Lacodix\LaravelPlans\Models\Traits\ConsumesFeatures;
use LogicException;

/**
 * @property int $plan_id
 * @property Subscriber $subscriber
 * @property string $slug
 * @property ?Carbon $starts_at
 * @property ?Carbon $trial_ends_at
 * @property ?Carbon $ends_at
 * @property ?Carbon $canceled_for
 * @property ?Carbon $canceled_at
 * @property Plan $plan
 */
class Subscription extends Model
{
    use SoftDeletes;
    use ConsumesFeatures;

    protected $fillable = [
        'plan_id',
        'subscriber_id',
        'subscriber_type',
        'slug',
        'starts_at',
        'trial_ends_at',
        'ends_at',
        'canceled_for',
        'canceled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_for' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('plans.tables.subscriptions', 'subscriptions');
    }

    /**
     * @return MorphTo<Model, Subscription>
     */
    public function subscriber(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<FeatureUsage>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(config('plans.models.feature_usage'));
    }

    /**
     * @return BelongsTo<Plan, Subscription>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('plans.models.plan'));
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeByPlanId(Builder $builder, int $planId): Builder
    {
        return $builder->where('plan_id', $planId);
    }

    public function active(?Carbon $date = null): bool
    {
        return ! $this->ended($date);
    }

    public function inactive(?Carbon $date = null): bool
    {
        return ! $this->active($date);
    }

    public function onTrial(?Carbon $date = null): bool
    {
        $date ??= now();

        return $this->trial_ends_at && $date->lt($this->trial_ends_at);
    }

    public function canceled(?Carbon $date = null): bool
    {
        $date ??= now();

        return $this->canceled_at && $date->gte($this->canceled_at);
    }

    public function ended(?Carbon $date = null): bool
    {
        $date ??= now();

        return $this->ends_at && $date->gte($this->ends_at);
    }

    public function cancel(?Carbon $date = null): void
    {
        $this->canceled_for = $date ?? now();
        $this->canceled_at = now();
        $this->save();
    }

    public function cancelAndDelete(): void
    {
        $this->cancel();
        $this->delete();
    }

    public function renew(bool $force = false): static
    {
        if ($this->ended() && $this->canceled()) {
            throw new LogicException('Unable to renew canceled subscriptions.');
        }

        if (! $this->ended() && ! $force) {
            return $this;
        }

        DB::beginTransaction();

        // Clear current usage
        $this->usages()->delete();

        // renew to next period
        $this->setNewPeriod(start: $this->ends_at->clone()->addSecond());
        $this->canceled_at = null; // might be canceled, but now, renewed.
        $this->canceled_for = null;
        $this->save();

        DB::commit();

        return $this;
    }

    public function getFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->toArray();
    }

    public function getUncountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => is_null($value))
            ->keys()
            ->toArray();
    }

    public function getCountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => ! is_null($value))
            ->toArray();
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeEndingTrial(Builder $builder, int $dayRange = 3): Builder
    {
        return $builder->whereBetween('trial_ends_at', [now(), now()->addDays($dayRange)]);
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeEndedTrial(Builder $builder): Builder
    {
        return $builder->where('trial_ends_at', '<=', now());
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeEnding(Builder $builder, int $dayRange = 3): Builder
    {
        return $builder->whereBetween('ends_at', [now(), now()->addDays($dayRange)]);
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeEnded(Builder $builder): Builder
    {
        return $builder->where('ends_at', '<=', now());
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('ends_at', '>', now());
    }

    /**
     * @return Collection<string, ?int>
     */
    protected function getSluggedFeatures(): Collection
    {
        return $this->plan
            ->features
            ->mapWithKeys(static fn (Feature $feature) => [$feature->slug => $feature->pivot->value]);
    }

    protected function setNewPeriod(?Interval $billingInterval = null, ?int $billingPeriod = null, ?Carbon $start = null): static
    {
        $period = new Period(
            interval: $billingInterval ?? $this->plan->billing_interval,
            count: $billingPeriod ?? $this->plan->billing_period,
            start: $start ?? now(),
            synced: config('plans.sync_subscriptions'),
        );

        $this->starts_at = $period->getStartDate();
        $this->ends_at = $period->getEndDate();

        return $this;
    }

    protected static function booted(): void
    {
        static::creating(static function (self $subscription): void {
            if (! $subscription->starts_at || ! $subscription->ends_at) {
                $subscription->setNewPeriod();
            }
        });

        static::deleted(static function (self $subscription): void {
            $subscription->usages()->delete();
        });
    }
}
