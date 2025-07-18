<?php

namespace Database\Factories;

use App\Models\Station;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cell>
 */
class CellFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cell_number' => fake()->unique()->randomNumber(5, true), // Generates a unique 5-digit number
            'block' => 'BLOCK ' . strtoupper(Str::random(1)),
            'station_id' => Station::inRandomOrder()->first()->id, // Ensures valid foreign key
        ];
    }
}
