<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::factory(7)->create();
        Category::factory(5)->create();
        Product::factory(20)->create();
    }
}
