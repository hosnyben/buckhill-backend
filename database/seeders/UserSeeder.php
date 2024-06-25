<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        if(!User::where('email', 'admin@buckhill.co.uk')->exists()) {
            User::factory()->create([
                'email' => 'admin@buckhill.co.uk',
                'password' => Hash::make('admin'),
                'is_admin' => true,
            ]);
        }

        // Create accounts with marketing preferences
        User::factory(5)->create([
            'is_marketing' => true,
        ]);

        // Create regular accounts
        User::factory(10)->create();
    }
}
