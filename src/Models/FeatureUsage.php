<?php

namespace Lacodix\LaravelPlans\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $subscription_id
 * @property int $feature_id
 * @property ?int $used
 * @property ?Carbon $valid_until
 */
class FeatureUsage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'feature_id',
        'used',
        'valid_until',
    ];

    public function getTable(): string
    {
        return config('plans.tables.feature_usages', 'feature_usages');
    }

    /**
     * @return BelongsTo<Subscription, FeatureUsage>
     */
    public function subscription(): BelongsTo
    {
        // @phpstan-ignore-next-line - phpstan doesn't detect the class string behind config
        return $this->belongsTo(config('plans.models.subscription'));
    }

    /**
     * @return BelongsTo<Feature, FeatureUsage>
     */
    public function feature(): BelongsTo
    {
        // @phpstan-ignore-next-line - phpstan doesn't detect the class string behind config
        return $this->belongsTo(config('plans.models.feature'));
    }

    public function expired(): bool
    {
        if (! $this->valid_until) {
            return false;
        }

        return Carbon::now()->gte($this->valid_until);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valid_until' => 'datetime',
        ];
    }
}
