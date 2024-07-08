<?php

use Illuminate\Support\Facades\Event;
use Lacodix\LaravelPlans\Events\PlanChanged;
use Lacodix\LaravelPlans\Events\PlanSubscribed;
use Lacodix\LaravelPlans\Models\Feature;
use Lacodix\LaravelPlans\Models\Plan;
use Tests\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('sends subscription event', function () {
    $plan = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    Event::fake();

    $sub = $this->user->subscribe($plan);

    Event::assertDispatched(fn (PlanSubscribed $event) => $event->subscription->id === $sub->id && !$event->oldSubscription instanceof \Lacodix\LaravelPlans\Models\Subscription);
});

it('sends subscription event with old subscription', function () {
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

    Event::fake();

    $sub2 = $this->user->subscribe($plan2); // deletes first subscription, creates new one, no new trial

    Event::assertDispatched(fn (PlanSubscribed $event) => $event->subscription->id === $sub2->id && $event->oldSubscription->id === $sub1->id);
});

it('sends plan change event on feature add', function () {
    $plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 1,
        'trial_interval' => 'month',
        'grace_period' => 0,
    ])->create();

    $feature1 = Feature::factory([
        'slug' => 'my_feature',
    ])->create();

    Event::fake(PlanChanged::class);

    $plan1->features()->attach($feature1);

    Event::assertDispatched(fn (PlanChanged $event) => $event->plan->id === $plan1->id);
});

it('sends plan change event on feature update', function () {
    $plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 1,
        'trial_interval' => 'month',
        'grace_period' => 0,
    ])->create();

    $feature1 = Feature::factory([
        'slug' => 'my_feature',
    ])->create();

    $plan1->features()->attach($feature1);

    Event::fake(PlanChanged::class);

    $plan1->features()->updateExistingPivot($feature1->id, [
        'value' => 5
    ]);

    Event::assertDispatched(fn (PlanChanged $event) => $event->plan->id === $plan1->id);
});

it('sends plan change event on feature removal', function () {
    $plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 1,
        'trial_interval' => 'month',
        'grace_period' => 0,
    ])->create();

    $feature1 = Feature::factory([
        'slug' => 'my_feature',
    ])->create();

    $plan1->features()->attach($feature1);

    Event::fake(PlanChanged::class);

    $plan1->features()->detach($feature1);

    Event::assertDispatched(fn (PlanChanged $event) => $event->plan->id === $plan1->id);
});
