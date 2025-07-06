<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sentence>
 */
class SentenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $epd = $this->faker->optional()->dateTimeBetween('now', '+5 years');
        $lpd = $this->faker->optional()->dateTimeBetween('+5 years', '+10 years');
        $dateOfAmnesty = $this->faker->optional()->dateTimeBetween('-2 years', 'now');
        $dateOfSentence = $this->faker->dateTimeBetween('-5 years', 'now');

        return [
            'inmate_id' => \App\Models\Inmate::factory(),
            'sentence' => $this->faker->randomElement(['5 years', '10 years', 'Life']),
            'total_sentence' => $this->faker->randomElement(['5 years', '10 years', 'Life']),
            'reduced_sentence' => $this->faker->optional()->randomElement(['3 years', '7 years']),
            'offence' => $this->faker->randomElement(['Assault', 'Theft', 'Robbery', 'Murder']),
            'EPD' => $epd?->format('Y-m-d'),
            'LPD' => $lpd?->format('Y-m-d'),
            'court_of_committal' => $this->faker->city . ' High Court',
            'commutted_by' => $this->faker->optional()->randomElement(['President', 'Appeals Court']),
            'commutted_sentence' => $this->faker->optional()->randomElement(['2 years', 'Life', '10 years']),
            'date_of_sentence' => $dateOfSentence->format('Y-m-d'),
            'date_of_amnesty' => $dateOfAmnesty?->format('Y-m-d'),
            'amnesty_document' => $this->faker->optional()->word() . '.pdf',
            'warrant_document' => $this->faker->optional()->word() . '.pdf',
        ];
    }
}
