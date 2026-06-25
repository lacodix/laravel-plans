<?php

use Lacodix\LaravelPlans\Models\Plan;
use Tests\Models\User;

use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    $this->user = User::factory()->create();
    config()->set('plans.sync_subscriptions', false);
});

$plainPlan = fn (array $meta) => Plan::factory([
    'billing_period' => 1,
    'billing_interval' => 'month',
    'trial_period' => 0,
    'grace_period' => 0,
    'meta' => $meta,
])->create();

it('falls back to the plan meta when the subscription has no override', function () use ($plainPlan) {
    $sub = $this->user->subscribe($plainPlan(['price_per_member' => 0.05]));

    expect($sub->effectiveMeta('price_per_member'))->toBe(0.05);
});

it('lets the subscription meta override the plan meta', function () use ($plainPlan) {
    $sub = $this->user->subscribe($plainPlan(['price_per_member' => 0.05]), meta: ['price_per_member' => 0.03]);

    expect($sub->effectiveMeta('price_per_member'))->toBe(0.03);
});

it('returns the default when neither plan nor subscription define the key', function () use ($plainPlan) {
    $sub = $this->user->subscribe($plainPlan([]));

    expect($sub->effectiveMeta('missing', 42))->toBe(42);
});

test('isInTrial checks against the period start by default', function () {
    testTime()->freeze('2020-01-01 12:00:00');

    $plan = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 1,
        'trial_interval' => 'month',
        'grace_period' => 0,
    ])->create();

    $sub = $this->user->subscribe($plan);

    // Period starts 2020-01-01, trial ends 2020-01-31 -> still in trial at start.
    expect($sub->isInTrial())->toBeTrue();

    // A moment after the trial ends is no longer in trial.
    expect($sub->isInTrial(now()->parse('2020-02-15')))->toBeFalse();
});

test('isInTrial is false without a trial', function () {
    $plan = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $sub = $this->user->subscribe($plan);

    expect($sub->isInTrial())->toBeFalse();
});
