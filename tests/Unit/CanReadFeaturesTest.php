<?php

use Lacodix\LaravelPlans\Models\Feature;
use Lacodix\LaravelPlans\Models\Plan;
use function Spatie\PestPluginTestTime\testTime;
use Tests\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    testTime()->freeze('2020-01-01 12:00:00');

    $plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $plan2 = Plan::factory([
        'billing_period' => 12,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->sub1 = $this->user->subscribe($plan1, 'plan1');
    $this->sub2 = $this->user->subscribe($plan2, 'plan2');

    $feature = Feature::factory([
        'slug' => 'uncountable_feature',
    ])->create();

    $plan1->features()->attach($feature, [
        'value' => null,
    ]);

    $feature = Feature::factory([
        'slug' => 'infinite_feature',
    ])->create();

    $plan1->features()->attach($feature, [
        'value' => -1,
    ]);

    $feature = Feature::factory([
        'slug' => 'countable_and_infinite_feature',
    ])->create();

    $plan1->features()->attach($feature, [
        'value' => 5,
    ]);
    $plan2->features()->attach($feature, [
        'value' => -1,
    ]);

    $feature = Feature::factory([
        'slug' => 'countable_and_uncountable_feature',
    ])->create();

    $plan1->features()->attach($feature, [
        'value' => 8,
    ]);
    $plan2->features()->attach($feature, [
        'value' => null,
    ]);

    $feature = Feature::factory([
        'slug' => 'countable_feature',
    ])->create();
    $plan2->features()->attach($feature, [
        'value' => 10,
    ]);
});

it('can read full features from subscriptions', function () {
    expect($this->sub1->getFeatures())->toEqual([
        'uncountable_feature' => -2,
        'infinite_feature' => -1,
        'countable_and_infinite_feature' => 5,
        'countable_and_uncountable_feature' => 8,
    ])
        ->and($this->sub2->getFeatures())->toEqual([
            'countable_and_infinite_feature' => -1,
            'countable_and_uncountable_feature' => -2,
            'countable_feature' => 10,
        ]);
});

it('can read all uncountable features', function () {
    expect($this->sub1->getUncountableFeatures())->toEqual([
        'uncountable_feature',
    ])
        ->and($this->sub2->getUncountableFeatures())->toEqual([
            'countable_and_uncountable_feature',
        ]);
});

it('can read all countable features', function () {
    expect($this->sub1->getCountableFeatures())->toEqual([
        'infinite_feature' => -1,
        'countable_and_infinite_feature' => 5,
        'countable_and_uncountable_feature' => 8
    ])
        ->and($this->sub2->getCountableFeatures())->toEqual([
            'countable_and_infinite_feature' => -1,
            'countable_feature' => 10,
        ]);
});

it('can read uncountable features on subscriber', function () {
    expect($this->user->getUncountableFeatures())->toEqual([
        'uncountable_feature',
        'countable_and_uncountable_feature'
    ]);
});

it('can read countable features on subscriber', function () {
    expect($this->user->getCountableFeatures())->toEqual([
        'infinite_feature' => -1,
        'countable_and_infinite_feature' => -1,
        'countable_feature' => 10,
    ]);
});
