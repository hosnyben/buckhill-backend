<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $validFrom = fake()->dateTime();
        $validTo = fake()->dateTimeBetween($validFrom, '+1 month');

        return [
            'title' => fake()->sentence(),
            'content' => fake()->sentence(),
            'metadata' => [
                'valid_from' => $validFrom->format('Y-m-d'),
                'valid_to' => $validTo->format('Y-m-d'),
                'image' => File::factory()->create()->uuid,
            ],
        ];
    }
}
