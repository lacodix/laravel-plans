<?php

namespace Lacodix\LaravelPlans\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Lacodix\LaravelPlans\Events\PlanSubscribed;
use Lacodix\LaravelPlans\Exceptions\FeatureNotAvailable;
use Lacodix\LaravelPlans\Models\Plan;
use Lacodix\LaravelPlans\Models\Subscription;
use LogicException;

/**
 * @property Collection<int, Subscription> $subscriptions
 */
trait HasSubscriptions
{
    use HasCountableAndUncountableFeatures;

    /**
     * @return MorphMany<Subscription>
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(config('plans.models.subscription'), 'subscriber')
            ->ordered();
    }

    public function subscribe(Plan $plan, $slug = 'default', ?int $order = null, ?array $meta = null): Subscription
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

        $newSubscription = $this->subscriptions()->create([
            'plan_id' => $plan->id,
            'slug' => $slug,
            'trial_ends_at' => $subscription?->trial_ends_at ?? $trialEndsAt,
            'meta' => $meta,
        ]);

        if ($order !== null) {
            $newSubscription->moveTo($order);
        }

        PlanSubscribed::dispatch($newSubscription, $subscription);

        return $newSubscription;
    }

    public function canConsumeFeature(string $featureSlug, int $shallUse = 1): bool
    {
        $remaining = $this->remainingFeature($featureSlug);

        return $remaining < 0 || $remaining >= $shallUse;
    }

    public function consumeFeature(string $featureSlug, int $shallUse = 1)
    {
        $remaining = $this->remainingFeature($featureSlug);
        if ($remaining >= 0 && $remaining < $shallUse) {
            return false;
        }

        $this->subscriptions
            ->reduce(static function (int $shallUse, Subscription $subscription) use ($featureSlug) {
                // Nothing to use, ok, go back
                if ($shallUse === 0) {
                    return 0;
                }

                try {
                    $remaining = $subscription->remaining($featureSlug);
                    // No use left, so keep $shallUse, go to next
                    if ($remaining === 0) {
                        return $shallUse;
                    }

                    // Unlimited/Uncountable or enough, consume, and reduce $shallUse to 0
                    if (($remaining < 0) || ($remaining >= $shallUse)) {
                        $subscription->consume($featureSlug, $shallUse);

                        return 0;
                    }

                    // Use what is left
                    $subscription->consume($featureSlug, $remaining);

                    return $shallUse - $remaining;
                } catch (LogicException|FeatureNotAvailable) {
                }

                return $shallUse; // Didn't use any, so keep $shallUse
            }, $shallUse);

        return true;
    }

    public function remainingFeature(string $featureSlug)
    {
        return $this->subscriptions
            ->reduce(static function (int $carry, Subscription $subscription) use ($featureSlug) {
                if ($carry < 0) {
                    // At least one subscription was unlimited/uncountable - just keep unlimited
                    return $carry;
                }

                $ret = 0;
                try {
                    $ret = $subscription->remaining($featureSlug);

                    if ($ret < 0) {
                        // At least one subscription is unlimited/uncountable - switch to unlimited
                        return $ret;
                    }
                } catch (LogicException|FeatureNotAvailable) {
                }

                return $carry + $ret;
            }, 0);
    }

    /**
     * @return Collection<string, int>
     */
    public function getSluggedFeatures(): Collection
    {
        return $this->subscriptions
            ->flatMap(static fn (Subscription $subscription) => $subscription->plan->features->map->slug)
            ->mapWithKeys(fn (string $featureSlug) => [$featureSlug => $this->remainingFeature($featureSlug)]);
    }
}
