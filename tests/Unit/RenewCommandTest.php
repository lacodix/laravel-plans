<?php

use Lacodix\LaravelPlans\Models\Plan;
use function Spatie\PestPluginTestTime\testTime;
use Tests\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->plan2 = Plan::factory([
        'billing_period' => 2,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    testTime()->freeze('2020-01-01 12:00:00');
    $this->sub1 = $this->user->subscribe($this->plan1, 'sub1'); // 1 month
    $this->sub2 = $this->user->subscribe($this->plan1, 'sub2'); // 1 month and canceled
    $this->sub2->cancel();
    $this->sub3 = $this->user->subscribe($this->plan2, 'sub3'); // 2 months
    $this->sub4 = $this->user->subscribe($this->plan2, 'sub4'); // 2 months and canceled
    $this->sub4->cancel();
});

it('renews only ended and uncancelled subscriptions', function () {
    testTime()->freeze('2020-02-01 00:00:00');
    $this->artisan('plans:renew-subscriptions');

    expect($this->sub1->refresh())->period_ends_at->toBeCarbon('2020-02-29 23:59:59')
        ->and($this->sub2->refresh())->period_ends_at->toBeCarbon('2020-01-31 23:59:59')
            ->canceled_for->toBeCarbon('2020-01-31 23:59:59')
        ->and($this->sub3->refresh())->period_ends_at->toBeCarbon('2020-02-29 23:59:59')
        ->and($this->sub4->refresh())->period_ends_at->toBeCarbon('2020-02-29 23:59:59')
            ->canceled_for->toBeCarbon('2020-02-29 23:59:59');
});

it('renews all uncancelled subscriptions', function () {
    testTime()->freeze('2020-02-01 00:00:00');
    $this->artisan('plans:renew-subscriptions --force');

    expect($this->sub1->refresh())->period_ends_at->toBeCarbon('2020-02-29 23:59:59')
        ->and($this->sub2->refresh())->period_ends_at->toBeCarbon('2020-01-31 23:59:59')
        ->canceled_for->toBeCarbon('2020-01-31 23:59:59')
        ->and($this->sub3->refresh())->period_ends_at->toBeCarbon('2020-04-30 23:59:59')
        ->and($this->sub4->refresh())->period_ends_at->toBeCarbon('2020-02-29 23:59:59')
        ->canceled_for->toBeCarbon('2020-02-29 23:59:59');
});
