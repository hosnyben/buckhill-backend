<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Payments;
use App\Models\Product;
use App\Models\OrderStatus;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_uuid' => User::factory(),
            'order_status_uuid' => OrderStatus::factory(),
            'payment_uuid' => Payments::factory(),
            'products' => [
                [
                    'product_uuid' => Product::factory(),
                    'quantity' => fake()->numberBetween(1, 10),
                    'price' => fake()->randomFloat(2, 1, 1000),
                ]
            ],
            'address' => [
                'billing' => fake()->address,
                'shipping' => fake()->address
            ],
            'delivery_fee' => fake()->randomFloat(2, 1, 100),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'shipping_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
