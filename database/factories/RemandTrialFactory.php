<?php

namespace Database\Factories;

use App\Models\Cell;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RemandTrial>
 */
class RemandTrialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'station_id' => Station::inRandomOrder()->first()->id,
            'cell_id' => Cell::inRandomOrder()->first()->id,
            'serial_number' => $this->faker->unique()->bothify('SN####'),
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'offense' => $this->faker->randomElement([
                'assault',
                'causing_harm',
                'defilement',
                'defrauding',
                'manslaughter',
                'murder',
                'robbery',
                'stealing',
                'unlawful_damage',
                'unlawful_entry',
                'others'
            ]),
            'admission_date' => $this->faker->date(),
            'age_on_admission' => $this->faker->optional()->numberBetween(18, 80),
            'court' => $this->faker->company(),
            'detention_type' => $this->faker->randomElement(['remand', 'trial']),
            'next_court_date' => $this->faker->dateTimeBetween('-2 months', '+2 months')->format('Y-m-d'),
            'warrant' => $this->faker->optional()->word(),
            'country_of_origin' => $this->faker->country(),
            'police_station' => $this->faker->optional()->company(),
            'police_officer' => $this->faker->optional()->name(),
            'police_contact' => $this->faker->optional()->phoneNumber(),
            're_admission_date' => $this->faker->optional()->date(),
            'picture' => $this->faker->optional()->imageUrl(),
        ];
    }
}
