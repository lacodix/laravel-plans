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

    $this->sub1 = $this->user->subscribe($plan, 'test1');
    $this->sub2 = $this->user->subscribe($plan, 'test2');
    $this->sub3 = $this->user->subscribe($plan, 'test3');
    $this->sub4 = $this->user->subscribe($plan, 'test4');
    $this->sub5 = $this->user->subscribe($plan, 'test5');
    $this->sub6 = $this->user->subscribe($plan, 'test6');
    $this->sub7 = $this->user->subscribe($plan, 'test7');
});

it('can move to end', function () {
    $this->sub4->moveTo(10);

    expect($this->sub4->refresh())->order->toBe(7)
        ->and($this->sub5->refresh())->order->toBe(4)
        ->and($this->sub6->refresh())->order->toBe(5)
        ->and($this->sub7->refresh())->order->toBe(6)
        ->and($this->sub1->refresh())->order->toBe(1)
        ->and($this->sub2->refresh())->order->toBe(2)
        ->and($this->sub3->refresh())->order->toBe(3);
});

it('can move from start to end', function () {
    $this->sub1->moveTo(7);

    expect($this->sub1->refresh())->order->toBe(7)
        ->and($this->sub2->refresh())->order->toBe(1)
        ->and($this->sub3->refresh())->order->toBe(2)
        ->and($this->sub4->refresh())->order->toBe(3)
        ->and($this->sub5->refresh())->order->toBe(4)
        ->and($this->sub6->refresh())->order->toBe(5)
        ->and($this->sub7->refresh())->order->toBe(6);
});

it('can move to start', function () {
    $this->sub3->moveTo(1);

    expect($this->sub3->refresh())->order->toBe(1)
        ->and($this->sub1->refresh())->order->toBe(2)
        ->and($this->sub2->refresh())->order->toBe(3)
        ->and($this->sub4->refresh())->order->toBe(4)
        ->and($this->sub5->refresh())->order->toBe(5)
        ->and($this->sub6->refresh())->order->toBe(6)
        ->and($this->sub7->refresh())->order->toBe(7);
});

it('can move from end to start', function () {
    $this->sub7->moveTo(1);

    expect($this->sub7->refresh())->order->toBe(1)
        ->and($this->sub1->refresh())->order->toBe(2)
        ->and($this->sub2->refresh())->order->toBe(3)
        ->and($this->sub3->refresh())->order->toBe(4)
        ->and($this->sub4->refresh())->order->toBe(5)
        ->and($this->sub5->refresh())->order->toBe(6)
        ->and($this->sub6->refresh())->order->toBe(7);
});

it('can move from start to middle', function () {
    $this->sub1->moveTo(3);

    expect($this->sub1->refresh())->order->toBe(3)
        ->and($this->sub2->refresh())->order->toBe(1)
        ->and($this->sub3->refresh())->order->toBe(2)
        ->and($this->sub4->refresh())->order->toBe(4)
        ->and($this->sub5->refresh())->order->toBe(5)
        ->and($this->sub6->refresh())->order->toBe(6)
        ->and($this->sub7->refresh())->order->toBe(7);
});

it('can move from end to middle', function () {
    $this->sub7->moveTo(4);

    expect($this->sub7->refresh())->order->toBe(4)
        ->and($this->sub1->refresh())->order->toBe(1)
        ->and($this->sub2->refresh())->order->toBe(2)
        ->and($this->sub3->refresh())->order->toBe(3)
        ->and($this->sub4->refresh())->order->toBe(5)
        ->and($this->sub5->refresh())->order->toBe(6)
        ->and($this->sub6->refresh())->order->toBe(7);
});

it('can move up', function () {
    $this->sub3->moveTo(6);

    expect($this->sub3->refresh())->order->toBe(6)
        ->and($this->sub1->refresh())->order->toBe(1)
        ->and($this->sub2->refresh())->order->toBe(2)
        ->and($this->sub4->refresh())->order->toBe(3)
        ->and($this->sub5->refresh())->order->toBe(4)
        ->and($this->sub6->refresh())->order->toBe(5)
        ->and($this->sub7->refresh())->order->toBe(7);
});

it('can move down', function () {
    $this->sub6->moveTo(3);

    expect($this->sub6->refresh())->order->toBe(3)
        ->and($this->sub1->refresh())->order->toBe(1)
        ->and($this->sub2->refresh())->order->toBe(2)
        ->and($this->sub3->refresh())->order->toBe(4)
        ->and($this->sub4->refresh())->order->toBe(5)
        ->and($this->sub5->refresh())->order->toBe(6)
        ->and($this->sub7->refresh())->order->toBe(7);
});
