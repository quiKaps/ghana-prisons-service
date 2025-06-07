<?php

namespace Database\Seeders;

use App\Models\Cell;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Inmate;
use Illuminate\Database\Seeder;
use Database\Factories\CellFactory;
use Database\Seeders\StationSeeder;
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
        $batchSize = 1000; // Number of records per batch
        $totalRecords = 50000; // Total number of records to create

        for ($i = 0; $i < $totalRecords / $batchSize; $i++) {
            Inmate::factory($batchSize)->create();
        }

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
