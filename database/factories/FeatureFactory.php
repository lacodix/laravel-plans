<?php

namespace Lacodix\LaravelPlans\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lacodix\LaravelPlans\Models\Feature;

class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'description' => $this->faker->sentence,
        ];
    }
}
