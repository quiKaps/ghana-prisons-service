<?php

namespace Database\Factories;

use App\Models\Cell;
use App\Models\Station;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inmate>
 */
class InmateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'serial_number' => strtoupper(Str::random(10)),
            'full_name' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'married_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'age_on_admission' => $this->faker->numberBetween(18, 80),
            'offence' => $this->faker->randomElement([
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
            'sentence' => $this->faker->numberBetween(1, 50) . ' years',
            'admission_date' => $this->faker->date(),
            'date_sentenced' => $this->faker->date(),
            'previously_convicted' => $this->faker->boolean(),
            'previous_sentence' => $this->faker->numberBetween(1, 40),
            'previous_offence' => $this->faker->sentence(1),
            'previous_station_id' => Station::inRandomOrder()->first()->id,
            'station_id' => Station::inRandomOrder()->first()->id, // Foreign key from database
            'cell_id' => Cell::inRandomOrder()->first()->id,       // Foreign key from database
            'court_of_committal' => $this->faker->city,
            'EPD' => $this->faker->dateTimeBetween('tomorrow', '+5 years')->format('Y-m-d'),
            'LPD' => $this->faker->dateTimeBetween('tomorrow', '+10 years')->format('Y-m-d'),
            'prisoner_picture' => $this->faker->imageUrl(),
            'next_of_kin_name' => $this->faker->name,
            'next_of_kin_relationship' => $this->faker->randomElement(['spouse', 'parent', 'sibling', 'friend']),
            'next_of_kin_contact' => $this->faker->phoneNumber,
            'religion' => $this->faker->randomElement(['Christianity', 'Islam', 'Hinduism', 'Atheist']),
            'nationality' => $this->faker->country,
            'education_level' => $this->faker->randomElement(['no_formal', 'primary', 'secondary', 'tertiary']),
            'occupation' => $this->faker->jobTitle,
            'hometown' => $this->faker->city,
            'tribe' => $this->faker->word,
            // Using json_encode to store arrays as JSON strings for database compatibility
            'distinctive_marks' => json_encode($this->faker->randomElements(
                ['Tribal Mark', 'Scar', 'Tattoo', 'Birthmark', 'Burn', 'Mole', 'Missing Finger', 'Amputation', 'Piercing', 'None'],
                2
            )),
            // Using json_encode to store arrays as JSON strings for database compatibility
            'languages_spoken' => json_encode($this->faker->randomElements(['English', 'French', 'Spanish', 'None'], 2)),
            'disability' => $this->faker->boolean(),
            'disability_type' => json_encode($this->faker->randomElements([
                'Visual impairment',
                'Hearing impairment',
                'Physical disability',
                'Intellectual disability',
                'Mental health condition',
                'Speech impairment',
                'Learning disability',
                'Chronic illness',
                'Others'
            ], 2)),
            'police_name' => $this->faker->name,
            'police_station' => $this->faker->city,
            'police_contact' => $this->faker->phoneNumber,
            'goaler' => $this->faker->boolean(),


        ];
    }
}
