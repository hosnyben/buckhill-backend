<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = Storage::disk('files')->putFile('/', fake()->image());

        return [
            'name' => fake()->sentence(),
            'path' => $path,
            'size' => Storage::disk('files')->size($path),
            'type' => Storage::disk('files')->mimeType($path),
        ];
    }
}
