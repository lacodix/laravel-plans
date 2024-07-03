<?php

namespace Lacodix\LaravelPlans\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Lacodix\LaravelPlans\Enums\Interval;
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

    protected function casts(): array
    {
        return [
            'resettable_interval' => Interval::class,
        ];
    }
}
