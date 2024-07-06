<?php

return [

    'sync_subscriptions' => true,
    'price_precision' => 2,

    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    |
    |
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
    |
    */

    'models' => [
        'plan' => Lacodix\LaravelPlans\Models\Plan::class,
        'feature' => Lacodix\LaravelPlans\Models\Feature::class,
        'feature_plan' => Lacodix\LaravelPlans\Models\FeaturePlan::class,
        'feature_usage' => Lacodix\LaravelPlans\Models\FeatureUsage::class,
        'subscription' => Lacodix\LaravelPlans\Models\Subscription::class,
    ],
];
