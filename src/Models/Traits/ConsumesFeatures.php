<?php

namespace Lacodix\LaravelPlans\Models\Traits;

use Carbon\Carbon;
use Lacodix\LaravelPlans\Classes\Period;
use Lacodix\LaravelPlans\Exceptions\FeatureNotAvailable;
use Lacodix\LaravelPlans\Models\Feature;
use Lacodix\LaravelPlans\Models\FeaturePlan;
use Lacodix\LaravelPlans\Models\FeatureUsage;
use LogicException;

trait ConsumesFeatures
{
    public function consumed(string $featureSlug): int
    {
        $feature = $this->getFeatureBySlug($featureSlug);
        $usage = $this->getAndCheckUsage($feature);

        return $usage->used ?? 0;
    }

    public function remaining(string $featureSlug): int
    {
        $feature = $this->getFeatureBySlug($featureSlug);

        // Uncountable
        if ($feature->pivot->value === null) {
            return -2;
        }

        // Unlimited
        if ($feature->pivot->value === -1) {
            return -1;
        }

        return ($feature->pivot->value ?? 0) - $this->consumed($featureSlug);
    }

    public function canConsume(string $featureSlug, int $used = 1): bool
    {
        $remaining = $this->remaining($featureSlug);

        return $remaining >= $used || $remaining < 0;
    }

    public function consume(string $featureSlug, int $used = 1): FeatureUsage
    {
        if (! $this->canConsume($featureSlug, $used)) {
            throw new FeatureNotAvailable('Not enough usages remaining');
        }

        return $this->createAndSet($featureSlug, $used);
    }

    public function unconsume(string $featureSlug, int $used = 1): FeatureUsage
    {
        return $this->createAndSet($featureSlug, - $used);
    }

    public function setUsage(string $featureSlug, int $used): FeatureUsage
    {
        return $this->createAndSet($featureSlug, $used, false);
    }

    protected function createAndSet(string $featureSlug, int $used, bool $incremental = true): FeatureUsage
    {
        $feature = $this->getFeatureBySlug($featureSlug);
        $usage = $this->getAndCheckUsage($feature);

        $usage->used = $incremental ? $usage->used + $used : $used;
        $usage->save();

        $this->plan->unsetRelation('features');

        return $usage;
    }

    protected function getFeatureBySlug(string $featureSlug): Feature
    {
        $feature = $this->plan->features->where('slug', $featureSlug)->first();

        if (! $feature) {
            throw new LogicException("Feature {$featureSlug} not available in this plan.");
        }

        return $feature;
    }

    protected function getAndCheckUsage(Feature $feature): FeatureUsage
    {
        $usage = $feature->usages()->firstOrNew([
            'subscription_id' => $this->getKey(),
        ]);

        if ($feature->pivot->resettable_period) {
            // if not already set, find current valid_until date
            $usage->valid_until ??= $this->calculateResetDate($feature->pivot, $this->period_starts_at);

            while ($usage->expired()) {
                $usage->valid_until = $this->calculateResetDate($feature->pivot, $usage->valid_until->clone()->addSecond());
                $usage->used = 0;
            }
        }

        return $usage;
    }

    protected function calculateResetDate(FeaturePlan $featurePlan, ?Carbon $dateFrom = null): Carbon
    {
        $period = new Period(
            interval: $featurePlan->resettable_interval,
            count: $featurePlan->resettable_period,
            start: $dateFrom,
            synced: config('plans.sync_subscriptions'),
        );

        return $period->getEndDate();
    }
}
