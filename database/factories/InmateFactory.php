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
            'surname' => $this->faker->lastName,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'married_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'age_on_admission' => $this->faker->numberBetween(18, 80),
            'date_of_birth' => $this->faker->date(),
            'offence' => $this->faker->sentence(10),
            'sentence' => $this->faker->numberBetween(1, 50) . ' years',
            'admission_date' => $this->faker->date(),
            'date_sentenced' => $this->faker->date(),
            'previously_convicted' => $this->faker->boolean(),
            'station_id' => Station::inRandomOrder()->first()->id ?? null, // Foreign key from database
            'cell_id' => Cell::inRandomOrder()->first()->id ?? null,       // Foreign key from database
            'court_of_committal' => $this->faker->city,
            'EPD' => $this->faker->date(),
            'LPD' => $this->faker->date(),
            'photo' => $this->faker->imageUrl(),
            'fingerprint' => Str::random(20),
            'signature' => Str::random(20),
            'next_of_kin_name' => $this->faker->name,
            'next_of_kin_relationship' => $this->faker->randomElement(['spouse', 'parent', 'sibling', 'friend']),
            'next_of_kin_contact' => $this->faker->phoneNumber,
            'medical_conditions' => json_encode($this->faker->randomElements(['Diabetes', 'Hypertension', 'None'], 2)),
            'allergies' => json_encode($this->faker->randomElements(['Peanuts', 'Seafood', 'None'], 2)),
            'religion' => $this->faker->randomElement(['Christianity', 'Islam', 'Hinduism', 'Atheist']),
            'nationality' => $this->faker->country,
            'education_level' => $this->faker->randomElement(['no_formal', 'primary', 'secondary', 'tertiary']),
            'occupation' => $this->faker->jobTitle,
            'hometown' => $this->faker->city,
            'tribe' => $this->faker->word,
            'distinctive_marks' => $this->faker->sentence(3),
            'languages_spoken' => json_encode($this->faker->randomElements(['English', 'French', 'Spanish', 'None'], 2)),
            'disability' => $this->faker->boolean(),
            'disability_type' => $this->faker->optional()->word,
            'police_name' => $this->faker->name,
            'police_station' => $this->faker->city,
            'police_contact' => $this->faker->phoneNumber,
            'goaler' => $this->faker->boolean(),
            'goaler_document' => $this->faker->optional()->word,
            'warrant_document' => $this->faker->optional()->word,
        ];
    }
}
