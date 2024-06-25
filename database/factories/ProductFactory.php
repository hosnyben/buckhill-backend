<?php

namespace Database\Factories;

use App\Models\Category;
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
            'category_uuid' => Category::factory(),
            'uuid' => fake()->uuid(),
            'title' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 1, 1000),
            'description' => fake()->paragraph(),
            'metadata' => [],
        ];
    }
}
