---
title: Plans
weight: 2
---

To subscribe to your service you need at least one plan. It is totally up to you, if you want different
plans with different pricing and feature sets, or if you just want to keep track about subscribed or not.
For the latter, you just need one single plan.

```php 
use Lacodix\LaravelPlans\Enums\Interval;
use Lacodix\LaravelPlans\Models\Feature;
use Lacodix\LaravelPlans\Models\Plan;

$myPlan = Plan::create([
    'slug' => 'my-plan',
    'name' => 'My Plan',
]);
```

This creates a plan without any additional information. The default interval of renewing is a month, the default 
price is just 0.

A more detailed example is the following. Some of this fields are not used by this package. For example we don't
care about active and inactive, but you can use it to make plans visible or invisible to your users. The trial
period and grace period settings are also irrelevant for this package (except price calculation), but you can use 
it to give your users a free trial period in the beginning, and on the other 
side, after an ended subscription you could allow the usage for a grade period, if it is set. To grant access or
not is up to your application.

```php 
Plan::create([
    'slug' => 'my-plan',
    'name' => [
        'de' => 'Mein spezieller Plan',
        'en' => 'My special plan',
    ],
    'description' => [
        'de' => 'Lange Erklärung',
        'en' => 'Large explanation',
    ],
    'price' => 50.0,
    'active' => true,
    'signup_fee' => 5.0,
    'trial_period' => 1,
    'trial_interval' => Interval::MONTH,
    'billing_period' => 1,
    'billing_interval' => Interval::MONTH,
    'grace_period' => 7,
    'grace_interval' => Interval::DAY,
    'meta' => [
        'price_per_token' => 0.05,
        ...
    ],
]);
```

## Sort your plans

Our package implements [eloquent sortable](https://github.com/spatie/eloquent-sortable) package by spatie.
Ordering plans is useful if you want to show plans on your page in a dedicated order. Please see more
details in the documentation of sortable package.

```php
$plan->moveToStart();
$plan->moveToEnd();
...
```

## Sugar

For convenience there are some additional functions and scopes on plans:

```php
// returns true, if price is 0. But doesn't care about signup-fee and meta-data.
$plan->isFree(); 

$plan->hasTrialPeriod(); 
$plan->hasGracePeriod();

$plan->activate();
$plan->deactivage();

Plan::query()->active()->get();
Plan::query()->inactive()->get();
 ```
