<?php

use Lacodix\LaravelPlans\Exceptions\FeatureNotAvailable;
use Lacodix\LaravelPlans\Models\Feature;
use Lacodix\LaravelPlans\Models\Plan;
use function Spatie\PestPluginTestTime\testTime;

use Tests\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    testTime()->freeze('2020-01-01 12:00:00');

    $this->plan = Plan::factory([
        'billing_period' => 1,
        'billing_interval' => 'month',
        'trial_period' => 0,
        'grace_period' => 0,
    ])->create();

    $this->feature = Feature::factory([
        'slug' => 'my_feature',
    ])->create();

    $this->sub = $this->user->subscribe($this->plan);
});

it('cannot consume unavailable feature', function () {
    $this->sub->consume('my_feature');
})->throws(LogicException::class);

it('can consume uncountable feature', function () {
    $this->plan->features()->attach($this->feature);

    $this->sub->consume('my_feature');

    expect($this->sub->remaining('my_feature'))->toBe(-2);

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub->id,
        'feature_id' => $this->feature->id,
        'used' => 1,
        'valid_until' => null,
    ]);
});

it('can consume countable feature as long as available', function () {
    $this->plan->features()->attach($this->feature, [
        'value' => 3,
    ]);

    expect($this->sub->remaining('my_feature'))->toBe(3);

    $this->sub->consume('my_feature', 3);

    expect($this->sub->remaining('my_feature'))->toBe(0);

    $this->assertDatabaseHas('feature_usages', [
        'subscription_id' => $this->sub->id,
        'feature_id' => $this->feature->id,
        'used' => 3,
        'valid_until' => null,
    ]);
});

it('cannot consume countable features if already used', function () {
    $this->plan->features()->attach($this->feature, [
        'value' => 3,
    ]);
    $this->sub->consume('my_feature', 3);
    $this->sub->consume('my_feature');

})->throws(FeatureNotAvailable::class);

it('gets new usages if period is over', function () {
    $this->plan->features()->attach($this->feature, [
        'value' => 3,
        'resettable_period' => 1,
        'resettable_interval' => 'month',
    ]);

    $this->sub->consume('my_feature', 3);

    expect($this->sub->canConsume('my_feature'))->toBeFalse()
        ->and($this->sub->remaining('my_feature'))->toBe(0);

    testTime()->freeze('2020-01-31 12:00:00');

    expect($this->sub->canConsume('my_feature'))->toBeFalse()
        ->and($this->sub->remaining('my_feature'))->toBe(0);

    testTime()->freeze('2020-02-01 00:00:00');

    expect($this->sub->canConsume('my_feature'))->toBeTrue()
        ->and($this->sub->remaining('my_feature'))->toBe(3);

});
