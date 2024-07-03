<?php

use Lacodix\LaravelPlans\Models\Plan;
use function Spatie\PestPluginTestTime\testTime;
use Tests\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    config()->set('plans.sync_subscriptions', false);
});

it('can subscribe to plan', function () {
    testTime()->freeze('2020-01-01 12:00:00');

    $plan = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $sub = $this->user->subscribe($plan);

    $this->assertDatabaseHas('subscriptions', [
        'plan_id' => $plan->id,
        'subscriber_id' => $this->user->id,
        'subscriber_type' => User::class,
        'slug' => 'default',
        'starts_at' => '2020-01-01 00:00:00',
        'ends_at' => '2020-01-31 23:59:59',
        'trial_ends_at' => null,
        'canceled_at' => null,
        'canceled_for' => null,
    ]);
});

test('subscription gets trial period', function () {
    testTime()->freeze('2020-01-31 12:00:00');

    $plan = Plan::factory([
        'billing_period' => 3,
        'billing_interval' => 'month',
        'trial_period' => 1,
        'trial_interval' => 'month',
        'grace_period' => 0,
    ])->create();

    $this->user->subscribe($plan);

    $this->assertDatabaseHas('subscriptions', [
        'plan_id' => $plan->id,
        'subscriber_id' => $this->user->id,
        'subscriber_type' => User::class,
        'slug' => 'default',
        'starts_at' => '2020-01-31 00:00:00',
        'ends_at' => '2020-04-29 23:59:59',
        'trial_ends_at' => '2020-02-29 23:59:59',
        'canceled_at' => null,
        'canceled_for' => null,
    ]);
});

it('can subscribe to different plans', function () {
    testTime()->freeze('2020-01-01 12:00:00');

    $plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $plan2 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->user->subscribe($plan1);
    $this->user->subscribe($plan2, 'addon');

    $this->assertDatabaseCount('subscriptions', 2);
    $this->assertDatabaseHas('subscriptions', [
        'slug' => 'default',
        'plan_id' => $plan1->id,
    ]);
    $this->assertDatabaseHas('subscriptions', [
        'slug' => 'addon',
        'plan_id' => $plan2->id,
    ]);
});

it('cannot subscribe same slug twice', function () {
    testTime()->freeze('2020-01-01 12:00:00');

    $plan = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->user->subscribe($plan);
    testTime()->freeze('2020-01-15 12:00:00');

    $this->user->subscribe($plan); // Doesn't change anything

    $this->assertDatabaseCount('subscriptions', 1);
    $this->assertDatabaseHas('subscriptions', [
        'plan_id' => $plan->id,
        'subscriber_id' => $this->user->id,
        'subscriber_type' => User::class,
        'slug' => 'default',
        'starts_at' => '2020-01-01 00:00:00',
        'ends_at' => '2020-01-31 23:59:59',
        'trial_ends_at' => null,
        'canceled_at' => null,
        'canceled_for' => null,
    ]);
});

it('can change plan with same slug', function () {
    testTime()->freeze('2020-01-01 12:00:00');

    $plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 1,
        'trial_interval' => 'month',
        'grace_period' => 0,
    ])->create();

    $plan2 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 2,
        'trial_interval' => 'month',
        'grace_period' => 0,
    ])->create();

    $sub1 = $this->user->subscribe($plan1);
    testTime()->freeze('2020-01-15 12:00:00');

    $sub2 = $this->user->subscribe($plan2); // deletes first subscription, creates new one, no new trial

    $this->assertDatabaseCount('subscriptions', 2);
    $this->assertSoftDeleted($sub1);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $sub2->id,
        'plan_id' => $plan2->id,
        'subscriber_id' => $this->user->id,
        'subscriber_type' => User::class,
        'slug' => 'default',
        'starts_at' => '2020-01-15 00:00:00',
        'ends_at' => '2020-02-14 23:59:59',
        'trial_ends_at' => '2020-02-01 23:59:59',
        'canceled_at' => null,
        'canceled_for' => null,
    ]);
});
