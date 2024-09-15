---
title: Features
weight: 4
---

This is an optional feature of this package. If you want to have different plans, with different features
you can make use of it. You can use countable and uncountable features. Uncountable features might just 
unlock a functionality of your service, while countable features can be consumed as long as the amount is
available. It will automatically be refilled after a given period.

If a feature is countable or uncountable depends on the attachment to the plan. It is even possible to have
one single feature uncountable/unlimited in one plan but with a maximum value in another plan. 

First you have to create features and attach them to your plans.

```php 
$uncountableFeature = Feature::create([
    'slug' => 'api_access',
    'name' => [
        'de' => 'API Zugang',
        'en' => 'API access',
    ],
]);

$myPlan->features()->attach($uncountableFeature);

$countableFeature = Feature::create([
    'slug' => 'tokens',
    'name' => [
        'de' => 'Zusätzliche Tokens',
        'en' => 'Additional Tokens',
    ],
]);

$unlimitedFeature = Feature::create([
    'slug' => 'unlimited',
    'name' => 'unlimited',
]);

$myPlan->features()->attach($countableFeature, [
    'value' => 1000,
    'resettable_period' => 1,
    'resettable_interval' => Interval::MONTH,
]);

$myPlan->features()->attach($unlimitedFeature, [
    'value' => -1,
]);

```

## Get usable features and remaining amounts

After you attached features to your plans, you can consume them and check for availability.

```php 
    $features = $subscriber->getFeatures();

/* returns: 
[
    "api_access" => -2,
    "tokens" => 1000,
]*/ 
```

This will return the current remaining amount of each feature by slug. If a feature is attached to multiple plans, it will
be aggregated. If one plan for example contains an unlimited amount, the remaining amount is unlimited. 
-2 represents uncountable features, -1 unlimited features, and each other number is the remaining amount.
If you are just interested in the availability you can just check for != 0.

-2 and -1 are only of interest for internal uses, and for differ between uncountable and countable but unlimited featuers.
This is indeed used inside the following function that just returns the slugs of all uncuntable features:

```php 
    $features = $subscriber->getUncountableFeatures();

/* returns: 
[
    "api_access",
]*/ 
```

While this function will return all countable features, independent if there is an amount or unlimited

```php 
    $features = $subscriber->getCountableFeatures();

/* returns: 
[
    "tokens" => 1000,
]*/ 
```

## Get information about one single feature

```php 
    $features = $subscriber->remainingFeature('tokens'); // returns 1000;
    $features = $subscriber->remainingFeature('api_access'); // returns -2;
```

## Just get the features of one single plan

In case you need to offer detailed information about your plans, you might be interested in the features that are 
contained. For this use case you can use the same functions on a plan. This will return the features and the maximum
amount. It will not care about the reset interval of a feature, but for more details you can write your own function
and access the features relation on a plan.

```php 
    $plan->getFeatures();
    $plan->getUncountableFeatures();
    $plan->getCountableFeatures();
```

## Consume features

If you use countable features it is important to consume them. Everytime a subscriber uses a feature you can call
the following function.

```php 
    // consumes 1 token
    $subscriber->consumeFeature('tokens');
    $subscriber->remainingFeature('tokens'); // returns 999;
    
    // consume multiple tokens
    $subscriber->consumeFeature('tokens', 100);
    $subscriber->remainingFeature('tokens'); // returns 899;
```

If you have multiple subscriptions with the same feature, you can consume as many as you have in all subscriptions.
The package will first use all amount of the first subscription and use the rest of the subsequent subscriptions.

You can also check if a feature consume is possible right before you start an action - maybe to disable a use button.

```php 
    // checks for the usage of 1 token
    $subscriber->canConsumeFeature('tokens');
    
    // checks for the usage of multiple tokens
    $subscriber->canConsumeFeature('tokens', 100);
```

## Get information of a single subscription

All functions above are on the point of view of the subscriber, who might have multiple subscriptions containing the 
same feature. If you are interested in the detailed information of one single subscription - maybe to see, how much 
of the tokens are used in each subscription, you can access this information also.

