<?php

use Lacodix\LaravelPlans\Models\Plan;
use function Spatie\PestPluginTestTime\testTime;

use Tests\Models\User;

it('calculates the price correct with trial', function () {
    testTime()->freeze('2020-01-15 12:00:00');

    $user = User::factory()->create();
    $plan = Plan::factory([
        'billing_period' => 2,
        'billing_interval' => 'month',
        'trial_period' => 2,
        'trial_interval' => 'month',
        'grace_period' => 0,
        'price' => 50,
    ])->create();

    $sub = $user->subscribe($plan);

    // First time, we have to pay nothing, because trial is longer than billing period end
    expect($sub->calculatePeriodPrice())->toBe(0.0);

    testTime()->freeze('2020-03-01 00:00:00');
    $sub->renew();

    // Trial ends on 15th of first bi-month so we have to pay 1,5 months ~75%)
    expect($sub->calculatePeriodPrice())->toBe(38.5);

    testTime()->freeze('2020-05-01 00:00:00');
    $sub->renew();

    // Next Times full price
    expect($sub->calculatePeriodPrice())->toBe(50.0);
    testTime()->freeze('2020-07-01 00:00:00');
    $sub->renew();
    expect($sub->calculatePeriodPrice())->toBe(50.0);
});
