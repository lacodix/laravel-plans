<?php

namespace Lacodix\LaravelPlans\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lacodix\LaravelPlans\Database\Factories\FeatureFactory;
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
    /** @use HasFactory<FeatureFactory> */
    use HasFactory;
    use HasTranslations;

    /** @var array<int, string> */
    public array $translatable = [
        'name',
        'description',
    ];

    /** @var array<int, string> */
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
        // @phpstan-ignore-next-line - phpstan doesn't detect the class string behind config
        return $this->belongsToMany(config('plans.models.plan'))
            ->using(FeaturePlan::class)
            ->withPivot('order');
    }

    /**
     * @return HasMany<FeatureUsage>
     */
    public function usages(): HasMany
    {
        // @phpstan-ignore-next-line - phpstan doesn't detect the class string behind config
        return $this->hasMany(config('plans.models.feature_usage'));
    }

    /**
     * @return array<string, class-string|string>
     */
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
