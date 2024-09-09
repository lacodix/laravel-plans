<?php

namespace Lacodix\LaravelPlans\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lacodix\LaravelPlans\Database\Factories\PlanFactory;
use Lacodix\LaravelPlans\Enums\Interval;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property float $price
 * @property bool $active
 * @property float $signup_fee
 * @property int $trial_period
 * @property Interval $trial_interval
 * @property int $billing_period
 * @property Interval $billing_interval
 * @property int $grace_period
 * @property Interval $grace_interval
 * @property Collection<int,Feature> $features
 */
class Plan extends Model implements Sortable
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;
    use SortableTrait;
    use HasSlug;
    use HasTranslations;

    public array $translatable = [
        'name',
        'description',
    ];

    public array $sortable = [
        'order_column_name' => 'order',
    ];

    protected $fillable = [
        'slug',
        'name',
        'description',
        'price',
        'active',
        'signup_fee',
        'trial_period',
        'trial_interval',
        'billing_period',
        'billing_interval',
        'grace_period',
        'grace_interval',
        'meta',
    ];

    public function getTable(): string
    {
        return config('plans.tables.plans', 'plans');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->doNotGenerateSlugsOnUpdate()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * @return BelongsToMany<Feature>
     */
    public function features(): BelongsToMany
    {
        // @phpstan-ignore-next-line - phpstan doesn't detect the class string behind config
        return $this->belongsToMany(config('plans.models.feature'))
            ->using(FeaturePlan::class)
            ->withPivot('order', 'value', 'resettable_period', 'resettable_interval')
            ->orderByPivot('order');
    }

    /**
     * @return HasMany<Subscription>
     */
    public function subscriptions(): HasMany
    {
        // @phpstan-ignore-next-line - phpstan doesn't detect the class string behind config
        return $this->hasMany(config('plans.models.subscription'));
    }

    public function isFree(): bool
    {
        return $this->price <= 0.00;
    }

    public function hasTrialPeriod(): bool
    {
        return $this->trial_period > 0;
    }

    public function hasGracePeriod(): bool
    {
        return $this->grace_period > 0;
    }

    public function getFeatureBySlug(string $slug): ?Feature
    {
        return $this->features()
            ->where('slug', $slug)
            ->first();
    }

    public function activate(bool $active = true): static
    {
        $this->update(['active' => $active]);

        return $this;
    }

    public function deactivate(): static
    {
        return $this->activate(false);
    }

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'price' => 'float',
            'signup_fee' => 'float',
            'trial_interval' => Interval::class,
            'billing_interval' => Interval::class,
            'grace_interval' => Interval::class,
            'meta' => 'json',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(static function (Plan $plan): void {
            $plan->features()->detach(); // doesn't fire the deleting event on FeaturePlan

            $plan->subscriptions()->each(static function (Subscription $subscription): void {
                $subscription->cancelAndDelete();
            });
        });
    }
}
