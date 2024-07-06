<?php

use Lacodix\LaravelPlans\Models\Plan;
use function Spatie\PestPluginTestTime\testTime;
use Tests\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    testTime()->freeze('2020-01-01 12:00:00');

    $plan = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->sub = $this->user->subscribe($plan);
});

it('does not renew still running subscriptions', function () {
    testTime()->freeze('2020-01-14 12:00:00');

    $this->sub->renew();

    expect($this->sub->refresh())->period_ends_at->toBeCarbon('2020-01-31 23:59:59');
});

it('can force renewing running subscriptions', function () {
    testTime()->freeze('2020-01-14 12:00:00');

    $this->sub->renew(true);

    expect($this->sub->refresh())->period_ends_at->toBeCarbon('2020-02-29 23:59:59');
});

it('renews ended subscriptions', function () {
    testTime()->freeze('2020-02-01 00:00:00');

    $this->sub->renew();

    expect($this->sub->refresh())->period_ends_at->toBeCarbon('2020-02-29 23:59:59');
});

it('renews and uncancels canceled subscriptions', function () {
    testTime()->freeze('2020-01-14 12:00:00');

    $this->sub->cancel();

    expect($this->sub->refresh())
        ->canceled_at->not()->toBeNull()
        ->canceled_for->not()->toBeNull();

    $this->sub->renew(true);

    expect($this->sub->refresh())
        ->period_ends_at->toBeCarbon('2020-02-29 23:59:59')
        ->canceled_at->toBeNull()
        ->canceled_for->toBeNull();
});

it('fails on renewing canceled and ended subscriptions', function () {
    testTime()->freeze('2020-01-14 12:00:00');

    $this->sub->cancel();

    expect($this->sub->refresh())
        ->canceled_at->not()->toBeNull()
        ->canceled_for->not()->toBeNull();

    testTime()->freeze('2020-02-01 00:00:00');

    $this->sub->renew();
})->throws(LogicException::class);
