<?php

namespace Lacodix\LaravelPlans\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lacodix\LaravelPlans\Enums\Interval;
use Lacodix\LaravelPlans\Models\Plan;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'active' => $this->faker->boolean,

            'signup_fee' => $this->faker->randomFloat(2, 0, 1000),
            'trial_period' => $this->faker->numberBetween(0, 365),
            'trial_interval' => $this->faker->randomElement(array_column(Interval::cases(), 'value')),
            'billing_period' => $this->faker->numberBetween(1, 12),
            'billing_interval' => $this->faker->randomElement(array_column(Interval::cases(), 'value')),
            'grace_period' => $this->faker->numberBetween(0, 365),
            'grace_interval' => $this->faker->randomElement(array_column(Interval::cases(), 'value')),
        ];
    }

    public function inactive()
    {
        return $this->state(static function () {
            return [
                'active' => false,
            ];
        });
    }

    public function active()
    {
        return $this->state(static function () {
            return [
                'active' => true,
            ];
        });
    }
}
