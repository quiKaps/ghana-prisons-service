<?php

namespace Database\Seeders;

use App\Models\Cell;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Inmate;
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
        User::factory(1)->create();



        // Seed the database with station-related data
        $this->call(StationSeeder::class);
        Cell::factory(1000)->create();

        User::factory()->create([
            'name' => fake()->name(),
            'email' => 'ohene@gmail.com',
            'station_id' => 1,
            'user_type' => 'prison_admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        User::factory()->create([
            'name' => fake()->name(),
            'email' => 'ella@gmail.com',
            'station_id' => 2,
            'user_type' => 'prison_admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $batchSize = 1000; // Number of records per batch
        $totalRecords = 50000; // Total number of records to create

        for ($i = 0; $i < $totalRecords / $batchSize; $i++) {
            Inmate::factory($batchSize)->create();

            //Sentence::factory($batchSize)->create();
            //generate 1k remand and trial inmates
            RemandTrial::factory($batchSize)->create();
        }
    }
}
