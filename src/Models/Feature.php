<?php

namespace Lacodix\LaravelPlans\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lacodix\LaravelPlans\Enums\Interval;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * Class Feature
 *
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property int $resettable_period
 * @property Interval $resettable_interval
 * @property FeaturePlan $pivot
 */
class Feature extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    public function getTable(): string
    {
        return config('plans.tables.features', 'features');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->doNotGenerateSlugsOnUpdate()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * @return BelongsToMany<Plan>
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(config('plans.models.plan'))
            ->using(FeaturePlan::class)
            ->withPivot('order');
    }

    /**
     * @return HasMany<FeatureUsage>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(config('plans.models.feature_usage'));
    }

    protected function casts(): array
    {
        return [
            'resettable_interval' => Interval::class,
        ];
    }

    protected static function booted(): void
    {
        static::deleting(static function (Feature $feature): void {
            $feature->plans()->detach();
            $feature->usages()->delete();
        });
    }
}
