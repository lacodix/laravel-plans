---
title: Compute Tokens
weight: 1
---

## Szenario like Gitlab Compute Tokens

Given a SaaS that has a monthly plan with free compute tokens every months.
When a user has reached his limit, he is able to buy a one-time package of additional
tokens. This additional tokens are available additional to the monthly limit. 
But always when there are monthly tokens, this will be used first. The additional
tokens are only consumed, if the monthly limit is reached. The additional tokens
are usable for one year after buying.

## Implementation

### Create Plans for Monthly and One Time Tokens

```php
    $monthlyPlan = Plan::create([
        'slug' => 'monthly',
        'name' => [
            'en' => 'Monthly Plan',
        ],
        'billing_period' => 1,
        'billing_interval' => Interval::MONTH,
    ]);

    $additionalTokens = Plan::create([
        'slug' => 'additional_tokens',
        'name' => [
            'en' => 'Additional Tokens',
        ],
        'billing_period' => 1,
        'billing_interval' => Interval::YEAR,
    ]);
```

### Add Features to Plans

```php
    $tokens = Feature::create([
        'slug' => 'tokens',
        'name' => [
            'en' => 'Tokens',
        ],
    ])

    $monthlyPlan->features()->attach($tokens, [
        'value' => 1000,
        'resettable_period' => 1,
        'resettable_interval' => Interval::MONTH,
    ]);

    $additionalTokens->features()->attach($tokens, [
        'value' => 10000,
    ]);
```

Now we have two plans, one for monthly tokens and one for additional tokens.

### Subscribe to the monthly plan, and consume tokens

```php
    $user->subscribe($monthlyPlan);
    
    $user->consumeFeature('tokens', $amountOfTokensToConsume);
```

From now on, the user will be able to consume 1000 tokens per month, and will get 
new 1000 tokens every renewal.

### Subscribe to the additional plan

When the 1000 tokens are used and he needs more, he can subscribe to the additional
plan. Keep in mind that all subscriptions are renewed automatically. So if you 
want to simulate a one time buy, just subscribe and cancel immediately after.

```php
    $subscription = $user->subscribe($additionalTokens);
    $subscription->cancel();
    
    // Continue consuming
    $user->consumeFeature('tokens', $amountOfTokensToConsume);
```

This will ensure, that the additional plan is not renewed. Since the ordering of the plans
happens automatically (second subscription is second in the list), it is ensured, that always
the monthly tokens will be used first, when available.

If you have an additional buy first, and then a monthly buy, you have to ensure that your monthly
plan is first in order. 

```php
    $user->subscribe($monthlyPlan, order: 1);
```

### Unlimited time

If you want the user to be able to consume the additional tokens forever, just simulate it by
using a high billing period:

```php
    $additionalTokens = Plan::create([
        'slug' => 'additional_tokens',
        'name' => [
            'en' => 'Additional Tokens',
        ],
        'billing_period' => 9999,
        'billing_interval' => Interval::YEAR,
    ]);
```

If somebody complains about unusable tokens after this time, just ignore it :-) 
