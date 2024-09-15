---
title: Events
weight: 6
---

Since this package doesn't care about billing, you might want to react on subscriptions and renewals. For this usecase
this package fires events that you can listen for and react with e.g. creating an invoice. 

# PlanSubscribed

This event is fired when a new subscription is created. But also when an existing subscriptions is changed and
a new plan is subscribed in it.

The event contains the subscription and if available the old subscription model

```php
public Subscription $subscription
public ?Subscription $oldSubscription
```

With the subscription you can access the subscriber and the plan, and you can access all relevant price and meta
data to get needed information that is needed for billing.

The old subscription is only available if the subscription was already there, identified by the slug (defaults to 'default').
If it was updated, then $oldSubscription will contain the old data like ->plan and ->meta.

# PlanChanged

This event is fired when a plan is changed, e.g. when you change the price or the name or description. Sometimes you 
might react on it, maybe recalculations or inform users. It just contains the affected plan.

```php 
public Plan $plan
```

# SubscriptionRenewed

This event is fired when a subscription is renewed. It will contain the subscription via with you will be able to 
access the subscriber and the plan and all metadata that might be needed for billing.

```php 
public Subscription $subscription
```

# SubscriptionsRenewed

This event is only fired if you configured the renewal aggregation config(plans.aggregate_renewals) and if you use
the contained artisan command for automatic renewals. If this feature is enabled, the artisan command will aggregate
all renewable subscriptions by subscriber, and finally fire this event (additionally to and after the base Event
SubscriptionRenewed that is still fired for each subscription).
This event contains all affected subscriptions of a subscriber.

```php 
public Collection $subscriptions
```

