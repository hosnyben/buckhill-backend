<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class, // Create users
            ProductSeeder::class, // Create brands, categories, and products (Files too)
            OrderSeeder::class, // Create order statuses, orders and payments (Files too)
            PostSeeder::class, // Create posts (Files too)
            PromotionSeeder::class, // Create promotions (Files too)
        ]);
    }
}
