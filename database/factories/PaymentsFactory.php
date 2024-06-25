<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payments>
 */
class PaymentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['credit_card', 'cash_on_delivery', 'bank_transfer']);

        switch ($type) {
            case 'credit_card':
                $details = [
                    'holder_name' => fake()->name,
                    'number' => fake()->creditCardNumber,
                    'ccv' => fake()->numberBetween(100, 999),
                    'expire_date' => fake()->date('Y-m'),
                ];
                break;
            case 'cash_on_delivery':
                $details = [
                    'first_name' => fake()->firstName,
                    'last_name' => fake()->lastName,
                    'address' => fake()->address,
                ];
                break;
            case 'bank_transfer':
                $details = [
                    'swift' => strtoupper(fake()->lexify('????')),
                    'iban' => fake()->iban(),
                    'name' => fake()->name,
                ];
                break;
            default:
                $details = [];
        }

        return [
            'type' => $type,
            'details' => json_encode($details),
        ];
    }
}
