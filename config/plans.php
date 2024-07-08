<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Synchronization
    |--------------------------------------------------------------------------
    |
    | Subscriptions are synced to the interval of your plan, independent of the
    | start date of the subscription. Even if you subscribe on the last day of
    | the month, it will be renewed already on the next day and be in sync to the
    | month. This engages you to invoice all of your users once a month. Same
    | for yearly, weekly, quarterly and so on.
    | If you want to invoice users immediately and the same day in next month,
    | just disable syncing.
    */
    'sync_subscriptions' => true,

    /*
    |--------------------------------------------------------------------------
    | Prices
    |--------------------------------------------------------------------------
    | Price calculation is rounded to 2 decimal places, you can also use 4 or
    | other numbers.
    */
    'price_precision' => 2,

    /*
    |--------------------------------------------------------------------------
    | Aggregate renewals
    |--------------------------------------------------------------------------
    | When renewing subscriptions, each subscription will be renewed on its
    | own and an event will be triggered for each subscription.
    | If you need only one event for all subscriptions of a user, just enable
    | aggregation. This will group renewed subscriptions by user and fire only
    | one event per user.
    */
    'aggregate_renewals' => false,

    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    */
    'tables' => [
        'plans' => 'plans',
        'features' => 'features',
        'feature_plan' => 'feature_plan',
        'feature_usages' => 'feature_usages',
        'subscriptions' => 'subscriptions',
        'model_has_subscriptions' => 'model_has_subscriptions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Models
    |--------------------------------------------------------------------------
    |
    | Models used to manage subscriptions. You can replace to use your own models,
    | but make sure that you have the same functionalities or that your models
    | extend from each model that you are going to replace.
    */
    'models' => [
        'plan' => Lacodix\LaravelPlans\Models\Plan::class,
        'feature' => Lacodix\LaravelPlans\Models\Feature::class,
        'feature_plan' => Lacodix\LaravelPlans\Models\FeaturePlan::class,
        'feature_usage' => Lacodix\LaravelPlans\Models\FeatureUsage::class,
        'subscription' => Lacodix\LaravelPlans\Models\Subscription::class,
    ],
];
