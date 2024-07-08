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
use Lacodix\LaravelPlans\Events\SubscriptionRenewed;
use Lacodix\LaravelPlans\Models\Traits\ConsumesFeatures;
use Lacodix\LaravelPlans\Models\Traits\SortableMoveTo;
use LogicException;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $plan_id
 * @property Subscriber $subscriber
 * @property int $subscriber_id
 * @property string $subscriber_type
 * @property string $slug
 * @property ?Carbon $started_at
 * @property ?Carbon $trial_ends_at
 * @property ?Carbon $period_starts_at
 * @property ?Carbon $period_ends_at
 * @property ?Carbon $canceled_for
 * @property ?Carbon $canceled_at
 * @property Plan $plan
 */
class Subscription extends Model implements Sortable
{
    use SoftDeletes;
    use ConsumesFeatures;
    use SortableMoveTo;

    public array $sortable = [
        'order_column_name' => 'order',
    ];

    protected $fillable = [
        'plan_id',
        'subscriber_id',
        'subscriber_type',
        'slug',
        'started_at',
        'trial_ends_at',
        'period_starts_at',
        'period_ends_at',
        'canceled_for',
        'canceled_at',
        'billed_until',
        'meta',
    ];

    /**
     * @return Builder<Subscription>
     */
    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('subscriber_type', $this->subscriber_type)
            ->where('subscriber_id', $this->subscriber_id);
    }

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

        return $this->period_ends_at && $date->gte($this->period_ends_at);
    }

    public function cancel(?Carbon $date = null): void
    {
        $this->canceled_for = $date ?? $this->period_ends_at;
        $this->canceled_at = now();
        $this->save();
    }

    public function cancelAndDelete(): void
    {
        $this->cancel();
        $this->delete();
    }

    /**
     * @return false|Collection<int,FeatureUsage>
     */
    public function renew(bool $force = false): false|Collection
    {
        if ($this->ended() && $this->canceled()) {
            throw new LogicException('Unable to renew canceled subscriptions.');
        }

        if (! $this->ended() && ! $force) {
            return false;
        }

        DB::beginTransaction();

        // Save and clear current usage
        $usages = $this->usages;
        $this->usages()->delete();

        // renew to next period
        $this->setNewPeriod(start: $this->period_ends_at->clone()->addSecond());
        $this->canceled_at = null; // might be canceled, but now, renewed.
        $this->canceled_for = null;
        $this->save();

        DB::commit();

        // Usages are reset in database, but still in this object
        SubscriptionRenewed::dispatch($this);

        return $usages;
    }

    public function getFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->toArray();
    }

    public function getUncountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => $value === -2)
            ->keys()
            ->toArray();
    }

    public function getCountableFeatures(): array
    {
        return $this->getSluggedFeatures()
            ->filter(static fn (?int $value) => $value >= -1)
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
        return $builder->whereBetween('period_ends_at', [now(), now()->addDays($dayRange)]);
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeEnded(Builder $builder): Builder
    {
        return $builder->where('period_ends_at', '<=', now());
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('period_ends_at', '>', now());
    }

    /**
     * @param Builder<Subscription> $builder
     *
     * @return Builder<Subscription>
     */
    public function scopeUncanceled(Builder $builder): Builder
    {
        return $builder->whereNull('canceled_at');
    }

    public function calculatePeriodPrice(): float
    {
        $period = new Period(
            interval: $this->plan->billing_interval,
            count: $this->plan->billing_period,
            start: $this->period_starts_at,
            synced: config('plans.sync_subscriptions'),
        );

        return round(
            $this->plan->price * $period->getLengthInPercent($this->trial_ends_at) / 100,
            config('plans.price_precision', 2)
        );
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'period_starts_at' => 'datetime',
            'period_ends_at' => 'datetime',
            'canceled_for' => 'datetime',
            'canceled_at' => 'datetime',
            'billed_until' => 'datetime',
            'meta' => 'json',
        ];
    }

    /**
     * @return Collection<string, int>
     */
    protected function getSluggedFeatures(): Collection
    {
        return $this->plan
            ->features
            ->mapWithKeys(fn (Feature $feature) => [$feature->slug => $this->remaining($feature->slug)]);
    }

    protected function setNewPeriod(?Interval $billingInterval = null, ?int $billingPeriod = null, ?Carbon $start = null): static
    {
        $period = new Period(
            interval: $billingInterval ?? $this->plan->billing_interval,
            count: $billingPeriod ?? $this->plan->billing_period,
            start: $start ?? now(),
            synced: config('plans.sync_subscriptions'),
        );

        $this->period_starts_at = $period->getStartDate();
        $this->period_ends_at = $period->getEndDate();

        return $this;
    }

    protected static function booted(): void
    {
        static::creating(static function (self $subscription): void {
            if (! $subscription->period_starts_at || ! $subscription->period_ends_at) {
                $subscription->setNewPeriod();
            }

            $subscription->started_at = $subscription->period_starts_at; // keep first period-start forever
        });

        static::deleted(static function (self $subscription): void {
            $subscription->usages()->delete();
        });
    }
}
