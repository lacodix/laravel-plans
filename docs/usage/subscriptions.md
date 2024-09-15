---
title: Subscriptions
weight: 3
---

After your plan was created, you can subscribe users to it. For doing that you need to implement our
HasSubscriptions trait.

```php 
use Lacodix\LaravelPlans\Models\Traits\HasSubscriptions;

class User extends Authenticatable {
    use HasSubscriptions;
    
    ...
}
```

You can add this trait to all models you need it. Sometimes not users subscribe to plans, but for example
tenants or teams.

```php 
$myPlan1 = Plan::find(...);
$user->subscribe($myPlan1);
```

This example will just subscribe to the plan, depending on the config and the plan settings.
Subscribers can only subscribe to one single plan as long as you omit the second parameter which defines the
slug of the subscription (defaults to 'default'). With a slug given you can subscribe to as many subscriptions
as you want. 

This solution enables you to subscribe to the same type of plans multiple times and additionsl plans. Nevertheless
it comes with the effort of keeping track of the slugs. But finally you can also replace single subscriptions
with for example larger plans.

```php 
// Subscribe to multiple plans
$user->subscribe($myPlan1, 'main');
$user->subscribe($myPlan2, 'addon');

// Change Subscription
$user->subscribe($myPlan3, 'main'); // will replace myPlan1 subscription

// Renew
$user->subscriptions()->first()->renew();

// Cancel
$user->subscriptions()->first()->cancel();
```

## Sort your subscriptions

Like plans, also our subscriptions implement the sortable functionality. See plans documentation for more
details. Sorting subscriptions might be useful to show the subscriptions of a single subscriber in order
and additionally it is responsible for the order how features are consumed from multiple plans, if they
all contain values of the same feature. Sorting is dependent on the subscriber.

A use case for sorting might be a feature like computing tokens, where your plan contains a dedicated amount of
computing tokens and for one month, and where you can buy additional tokens that will be used when the monthly 
amount is fully used. In such a use case it is important to first use the tokens of the monthly plan and afterwards
use the additional tokens.

```php 
$user->subscribe($myPlan2, 'addon', order: 1); // subscribe and set on first place
```

## Meta data

Like plans also subscriptions can contain meta-data. The usage of meta-data is totally up to you. 
It might be used for special pricing or additional price information or for example a discount that
is only available for this single subscriber.

```php 
$user->subscribe($myPlan1, meta: [
    'price': 5.00, 
    'discount': 0.1, // e.g. percent
]);
```

To make this meta data useful you have to read it in the renewal events and react on it.

## Renew Subscriptions

Subscriptions will end automatically, and nothing will happen magically. You have to call the renew function on 
an ended subscription if you want to renew it. With that possibility you can also sell one-time-usages like 1000 Tokens
that are valid for e.g. 1 year. In such a case it doesn't need to be renewed.

You can use the artisan command if you don't want to take care about renewals on your own. See [Renew Subscriptions](renew_subscriptions.md).

## Subscription data

a subscription contains additional data of the current period. You can receive the dates over properties:
```php
   $subscription->started_at; // the very first day of the subscription. 
   $subscription->period_starts_at; // contains the very first day or the first day after a renewal
   $subscription->period_ends_at; // the last dey before renewal.
   
   $subscription->canceled_for; // null or the date when the subscription will end and be canceled.
   $subscription->canceled_at; // null or the date when it was canceled;
   
   $subscription->trial_ends_at; // null or the date when an optional trial period will end/ended.
   
   $subscription->billed_until; // always null, free for your usage (see billing for more information).
``` 


## Sugar

For convenience there are some functions that you might use in a subsequent billing system or in a overview to get
more information about the state of the subscription.

```php 
    $subscription->active(); // returns true, when not ended already
    $subscription->inactive();

    $subscription->ended(); // returns true, when period_ends_at is over
    $subscription->ended(Carbon $onDate);

    $subscription->canceled(); // returns true, if subscription is canceled (but maybe not ended yet)

    $subscription->onTrial(); // If plan has a trial period and it is not ended yet
```

And some scopes
```php
    Subscription::query()->byPlanId($plan->id)->get();
     
    Subscription::query()->endingTrial($dayRange = 3)->get(); // when ending in the next 3 days (default = 3);
    Subscription::query()->endedTrial()->get();
    Subscription::query()->ending()->get(); // Ending means, it will can/must be renewed if period end is reached.
    
    Subscription::query()->active()->get(); // Still not ended
    Subscription::query()->canceled()->get();
    Subscription::query()->uncanceled()->get();
```
