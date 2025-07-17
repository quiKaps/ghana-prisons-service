<?php

namespace Database\Seeders;

use App\Models\Cell;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Inmate;
use App\Models\Station;
use App\Models\Sentence;
use App\Models\RemandTrial;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Database\Factories\CellFactory;
use Database\Seeders\StationSeeder;
use Illuminate\Support\Facades\Hash;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1️⃣ Create 3 stations first
        $stations =  $this->call(StationSeeder::class);

        // 2️⃣ Create 3 users, each tied to a specific station

        // Create Ohene (prison admin at station 1)
        User::firstOrCreate(
            ['email' => 'ohene@gmail.com'],
            [
                'name' => 'Ohene',
                'station_id' => 1,
                'serial_number' => "1111",
                'user_type' => 'prison_admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        // Create Ella (prison admin at station 1)
        User::firstOrCreate(
            ['email' => 'ella@gmail.com'],
            [
                'name' => 'Ella',
                'station_id' => 2,
                'serial_number' => "1122",
                'user_type' => 'prison_admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        // Create Admin (HQ admin, no station required)
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'station_id' => null,
                'serial_number' => "3333",
                'user_type' => 'hq_admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );



        // 3️⃣ For each station, create cells, inmates, sentences, and remand/trials
        // foreach ($stations as $station) {
        //     // Create 50 cells for this station
        //     $cells = Cell::factory()->count(50)->make();
        //     $station->cells()->saveMany($cells);

        //     // Seed inmates (majority active, some discharged)
        //     Inmate::factory(100)->make()->each(function ($inmate) use ($station, $cells) {
        //         $cell = $cells->random();
        //         $admissionDate = fake()->dateTimeBetween('-2 years', 'now');
        //         $isDischarged = fake()->boolean(10); // ~20% discharged

        //         $inmate->station()->associate($station);
        //         $inmate->cell()->associate($cell);
        //         $inmate->gender = $station->category;
        //         $inmate->is_discharged = $isDischarged;
        //         $inmate->created_at = $admissionDate;
        //         $inmate->updated_at = $admissionDate;
        //         $inmate->save();

        //         // Sentence
        //         $sentence = Sentence::factory()->make([
        //             'date_of_sentence' => fake()->dateTimeBetween($admissionDate, 'now')->format('Y-m-d'),
        //         ]);
        //         $inmate->sentences()->save($sentence);

        //         // Discharge (if applicable)
        //         if ($isDischarged) {
        //             $dischargeType = fake()->randomElement(['completed', 'parole', 'escape']);
        //             $inmate->discharge()->create([
        //                 'discharge_type' => $dischargeType,
        //                 'discharge_date' => fake()->dateTimeBetween($admissionDate, 'now')->format('Y-m-d'),
        //             ]);
        //         }
        //     });

        //     // Seed remand/trials (majority active, some discharged, some expired warrants)
        //     RemandTrial::factory(50)->make()->each(function ($remand) use ($station, $cells) {
        //         $cell = $cells->random();
        //         $admissionDate = fake()->dateTimeBetween('-2 years', 'now');
        //         $isDischarged = fake()->boolean(5); // ~25% discharged
        //         $isExpiredWarrant = fake()->boolean(15); // ~15% with expired court date

        //         $remand->station()->associate($station);
        //         $remand->cell()->associate($cell);
        //         $remand->gender = $station->category;
        //         $remand->is_discharged = $isDischarged;
        //         $remand->created_at = $admissionDate;
        //         $remand->updated_at = $admissionDate;

        //         // Set next court date
        //         $remand->next_court_date = $isExpiredWarrant
        //             ? fake()->dateTimeBetween('-6 months', '-1 day')->format('Y-m-d')
        //             : fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d');

        //         // Set discharge details if applicable
        //         if ($isDischarged) {
        //             $remand->mode_of_discharge = fake()->randomElement(['completed', 'bail', 'escape']);
        //             $remand->date_of_discharge = fake()->dateTimeBetween($admissionDate, 'now')->format('Y-m-d');
        //         }

        //         $remand->save();
        //     });
        // }
    }
}
