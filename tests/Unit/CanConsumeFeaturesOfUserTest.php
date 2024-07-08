<?php

use Lacodix\LaravelPlans\Models\Feature;
use Lacodix\LaravelPlans\Models\Plan;
use function Spatie\PestPluginTestTime\testTime;

use Tests\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    testTime()->freeze('2020-01-01 12:00:00');

    $this->plan1 = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->plan2 = Plan::factory([
        'billing_period' => 12,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->feature1 = Feature::factory([
        'slug' => 'my_feature',
    ])->create();

    $this->feature2 = Feature::factory([
        'slug' => 'second_feature',
    ])->create();

    $this->sub2 = $this->user->subscribe($this->plan2, 'addon');
    $this->sub1 = $this->user->subscribe($this->plan1, 'default', 1);
});

it('cannot consume unavailable feature', function () {
    expect($this->user->consumeFeature('my_feature'))->toBeFalse();
});

it('can consume feature available in one subscription', function () {
    $this->plan2->features()->attach($this->feature2);

    expect($this->user->consumeFeature('second_feature'))->toBeTrue()
        ->and($this->user->remainingFeature('second_feature'))->toBe(-2);
});

it('can consume countable feature as long as available', function () {
    $this->plan2->features()->attach($this->feature2, [
        'value' => 3,
    ]);

    expect($this->user->remainingFeature('second_feature'))->toBe(3);
    $this->user->consumeFeature('second_feature', 3);

    expect($this->user->remainingFeature('second_feature'))->toBe(0);

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub2->id,
        'feature_id' => $this->feature2->id,
        'used' => 3,
        'valid_until' => null,
    ]);
});

it('can consume unlimited+countable feature', function () {
    $this->plan1->features()->attach($this->feature2, [
        'value' => 3,
    ]);
    $this->plan2->features()->attach($this->feature2, [
        'value' => -1,
    ]);

    expect($this->user)
        ->remainingFeature('second_feature')->toBe(-1)
        ->consumeFeature('second_feature', 3)->toBeTrue()
        ->remainingFeature('second_feature')->toBe(-1)
        ->consumeFeature('second_feature', 3)->toBeTrue()
        ->consumeFeature('second_feature', 3)->toBeTrue();

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub1->id,
        'feature_id' => $this->feature2->id,
        'used' => 3,
        'valid_until' => null,
    ]);
    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub2->id,
        'feature_id' => $this->feature2->id,
        'used' => 6,
        'valid_until' => null,
    ]);
});

it('can consume countable feature from multiple subscriptions', function () {
    $this->plan1->features()->attach($this->feature2, [
        'value' => 3,
    ]);
    $this->plan2->features()->attach($this->feature2, [
        'value' => 3,
    ]);

    expect($this->user)
        ->remainingFeature('second_feature')->toBe(6)
        ->consumeFeature('second_feature', 5)->toBeTrue()
        ->remainingFeature('second_feature')->toBe(1)
        ->consumeFeature('second_feature', 3)->toBeFalse();

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub1->id,
        'feature_id' => $this->feature2->id,
        'used' => 3,
        'valid_until' => null,
    ]);
    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub2->id,
        'feature_id' => $this->feature2->id,
        'used' => 2,
        'valid_until' => null,
    ]);
});

it('fulfill monthly and onetime token usecase', function () {
    // we can use 100 Tokens per month, and we have a package of 1000 addon-Tokens
    // that can be used always when monthly tokens are empty.
    $this->plan1->features()->attach($this->feature2, [
        'value' => 100,
        'resettable_period' => 1,
        'resettable_interval' => 'month',
    ]);
    $this->plan2->features()->attach($this->feature2, [
        'value' => 1000,
    ]);

    expect($this->user)
        ->canConsumeFeature('second_feature')->toBeTrue()
        ->remainingFeature('second_feature')->toBe(1100)
        ->consumeFeature('second_feature', 200)->toBeTrue()
        ->remainingFeature('second_feature')->toBe(900);

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub1->id,
        'feature_id' => $this->feature2->id,
        'used' => 100,
        'valid_until' => '2020-01-31 23:59:59',
    ]);
    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub2->id,
        'feature_id' => $this->feature2->id,
        'used' => 100,
        'valid_until' => null,
    ]);

    // next month renews monthly tokens, but keeps onetime tokens
    testTime()->freeze('2020-02-01 00:00:00');
    expect($this->sub1->renew())->first()->used->toBe(100)
        ->and($this->sub2->renew())->toBe(false);

    expect($this->user)
        ->canConsumeFeature('second_feature')->toBeTrue()
        ->remainingFeature('second_feature')->toBe(1000)
        ->consumeFeature('second_feature', 500)->toBeTrue()
        ->remainingFeature('second_feature')->toBe(500);

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub1->id,
        'feature_id' => $this->feature2->id,
        'used' => 100,
        'valid_until' => '2020-02-29 23:59:59',
    ]);
    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub2->id,
        'feature_id' => $this->feature2->id,
        'used' => 500,
        'valid_until' => null,
    ]);

    // next month renews monthly tokens, but keeps onetime tokens
    testTime()->freeze('2020-03-01 00:00:00');
    expect($this->sub1->renew())->first()->used->toBe(100)
        ->and($this->sub2->renew())->toBe(false);

    expect($this->user)
        ->canConsumeFeature('second_feature')->toBeTrue()
        ->remainingFeature('second_feature')->toBe(600)
        ->consumeFeature('second_feature', 600)->toBeTrue()
        ->remainingFeature('second_feature')->toBe(0);

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub1->id,
        'feature_id' => $this->feature2->id,
        'used' => 100,
        'valid_until' => '2020-03-31 23:59:59',
    ]);
    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub2->id,
        'feature_id' => $this->feature2->id,
        'used' => 1000,
        'valid_until' => null,
    ]);

    // next month renews monthly tokens, but keeps onetime tokens
    testTime()->freeze('2020-04-01 00:00:00');
    expect($this->sub1->renew())->first()->used->toBe(100)
        ->and($this->sub2->renew())->toBe(false);

    expect($this->user)
        ->canConsumeFeature('second_feature')->toBeTrue()
        ->remainingFeature('second_feature')->toBe(100)
        ->consumeFeature('second_feature', 200)->toBeFalse();

    // just to test renewal
    testTime()->freeze('2021-01-01 00:00:00');
    expect($this->sub2->renew())->first()->used->toBe(1000);
});
