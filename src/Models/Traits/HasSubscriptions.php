<?php

namespace Lacodix\LaravelPlans\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Lacodix\LaravelPlans\Models\Plan;
use Lacodix\LaravelPlans\Models\Subscription;

/**
 * @property Collection<int, Subscription> $subscriptions
 */
trait HasSubscriptions
{
    /**
     * @return MorphMany<Subscription>
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(config('plans.models.subscription'), 'subscriber');
    }

    public function subscribe(Plan $plan, $slug = 'default'): Subscription
    {
        $subscription = $this->subscriptions()->where('slug', $slug)->first();

        if ($subscription) {
            if ($subscription->plan_id === $plan->id) {
                return $subscription;
            }

            $subscription->cancelAndDelete();
        }

        $trialEndsAt = null;
        if ($plan->trial_period > 0) {
            $trialEndsAt = now()
                ->add($plan->trial_interval->value, $plan->trial_period, false)
                ->endOfDay();
        }

        return $this->subscriptions()->create([
            'plan_id' => $plan->id,
            'slug' => $slug,
            'trial_ends_at' => $subscription?->trial_ends_at ?? $trialEndsAt,
        ]);
    }
}
