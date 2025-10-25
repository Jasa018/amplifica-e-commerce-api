<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 1000),
            'weight' => fake()->randomFloat(2, 0.1, 50),
            'width' => fake()->randomFloat(2, 10, 100),
            'height' => fake()->randomFloat(2, 10, 100),
            'length' => fake()->randomFloat(2, 10, 100),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
