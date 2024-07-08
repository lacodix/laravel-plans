<?php

namespace Lacodix\LaravelPlans\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Lacodix\LaravelPlans\Enums\Interval;
use Lacodix\LaravelPlans\Events\PlanChanged;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property ?int $value
 * @property int $order
 * @property ?int $resettable_period
 * @property ?Interval $resettable_interval
 */
class FeaturePlan extends Pivot implements Sortable
{
    use SortableTrait;

    public array $sortable = [
        'order_column_name' => 'order',
    ];

    protected $fillable = [
        'value',
        'order',
        'resettable_period',
        'resettable_interval',
    ];

    public function getTable(): string
    {
        return config('plans.tables.feature_plan', 'feature_plan');
    }

    /**
     * @return BelongsTo<Plan, FeaturePlan>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return BelongsTo<Feature, FeaturePlan>
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    protected function casts(): array
    {
        return [
            'resettable_interval' => Interval::class,
        ];
    }

    protected static function booted(): void
    {
        static::created(static function (self $model): void {
            PlanChanged::dispatch($model->plan);
        });

        static::updated(static function (self $model): void {
            PlanChanged::dispatch($model->plan);
        });

        static::deleted(static function (self $model): void {
            PlanChanged::dispatch($model->plan);
        });
    }
}
