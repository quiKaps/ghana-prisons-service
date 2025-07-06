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
            'prisoner_picture' => $this->faker->imageUrl(),
            'serial_number' => strtoupper(Str::random(10)),
            'full_name' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'married_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed', 'separated']),
            'age_on_admission' => $this->faker->numberBetween(18, 80),
            'admission_date' => $this->faker->date(),
            'previously_convicted' => $this->faker->boolean(),
            'previous_sentence' => $this->faker->optional()->numberBetween(1, 40),
            'previous_offence' => $this->faker->optional()->sentence(1),
            'previous_station_id' => $this->faker->optional()->randomNumber(),
            'station_id' => Station::inRandomOrder()->first()?->id,
            'cell_id' => Cell::inRandomOrder()->first()?->id,
            'court_of_committal' => $this->faker->optional()->city,
            // Next of kin
            'next_of_kin_name' => $this->faker->optional()->name,
            'next_of_kin_relationship' => $this->faker->optional()->randomElement(['spouse', 'parent', 'sibling', 'friend']),
            'next_of_kin_contact' => $this->faker->optional()->phoneNumber,
            // Personal details
            'religion' => $this->faker->optional()->randomElement(['Christianity', 'Islam', 'Hinduism', 'Atheist']),
            'nationality' => $this->faker->optional()->country,
            'education_level' => $this->faker->optional()->randomElement(['no_formal', 'primary', 'secondary', 'tertiary']),
            'occupation' => $this->faker->optional()->jobTitle,
            'hometown' => $this->faker->optional()->city,
            'tribe' => $this->faker->optional()->word,
            // Physical characteristics
            'distinctive_marks' => json_encode($this->faker->randomElements(
                ['Tribal Mark', 'Scar', 'Tattoo', 'Birthmark', 'Burn', 'Mole', 'Missing Finger', 'Amputation', 'Piercing', 'None'],
                2
            )),
            'part_of_the_body' => $this->faker->optional()->word,
            // Languages
            'languages_spoken' => json_encode($this->faker->randomElements(['English', 'French', 'Spanish', 'None'], 2)),
            // Disability
            'disability' => $this->faker->optional()->boolean,
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
            // Police details
            'police_name' => $this->faker->optional()->name,
            'police_station' => $this->faker->optional()->city,
            'police_contact' => $this->faker->optional()->phoneNumber,
            // Goaler
            'goaler' => $this->faker->optional()->boolean,
            'goaler_document' => json_encode([$this->faker->optional()->imageUrl()]),
            // Transfer details
            'transferred_in' => $this->faker->optional()->boolean,
            'station_transferred_from_id' => Station::inRandomOrder()->first()?->id,
            'date_transferred_in' => $this->faker->optional()->date,
            'transferred_out' => $this->faker->optional()->boolean,
            'station_transferred_to_id' => Station::inRandomOrder()->first()?->id,
            'date_transferred_out' => $this->faker->optional()->date,
            // Previous convictions
            'previous_convictions' => json_encode([
                [
                    'previous_offence' => $this->faker->word,
                    'previous_sentence' => $this->faker->numberBetween(1, 20) . ' years',
                    'previous_station_id' => Station::inRandomOrder()->first()?->id,
                ]
            ]),
            'is_discharged' => true,
        ];
    }
}
