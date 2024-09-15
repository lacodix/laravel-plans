---
title: Price per User
weight: 2
---

## Szenario

Sometimes you have a Saas that has to be paid per user. Maybe you offer a service for
teams or companies. The Company subscribes to your service and has multiple users.
You want them to pay a base price for the account overall, and additionally a fee for each
user (or every 10 users or so).

## Implementation

### Create the Plan with Meta Data

```php
    $monthlyPlan = Plan::create([
        'slug' => 'monthly',
        'name' => [
            'en' => 'Monthly Plan',
        ],
        'price' => 50.0,
        'billing_period' => 1,
        'billing_interval' => Interval::MONTH,
        'meta' => [
            'price_per_user' => 0.05,
        ],
    ]);
    
    $company->subscribe($monthlyPlan);
```

You can add features if you want.

Everytime when a subscription is renewed or subscribed, you get an event. In this event
you will retrieve the subscription itself. There you can evalutae all pricing information
and calculate the final price.

```php
// EventListener for the SubscriptionRenewed event
class CreateInvoice
{
    /**
     * @param Subscription $subscription
     */
    public function handle(Subscription $subscription): void
    {
        // Given you have the number of users already:
        $userCount = 15; // Could be like $subscription->subscriber->users()->count();
        
        // First get prices
        $price = $subscription->plan->price + ($userCount * $subscription->plan->meta['price_per_user']);
        
        // Calculate the period price if it is not a full month
        $part = $subscription->calculatePeriodLengthInPercent();
        $amountToPay = round($price * $part / 100, config('plans.price_precision', 2));
        
        // Create Invoice
        ...
        
        // Save the billed_until date for your use (optional).
        $subscription->billed_until = ...
        $subscription->save();
    }
}
```

Keep in mind that also in renewal it is possible to have partial months, if you use trial
periods. If you don't use trials, you can be sure that the period is always a full month with
the renewal and you don't need to calculate the length in percent. Then you only
need this calculation for a new subscription in the PlanSubscribed event.
