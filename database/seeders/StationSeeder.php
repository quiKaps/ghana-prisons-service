<?php

namespace Database\Seeders;

use App\Models\Cell;
use App\Models\User;
use App\Models\Inmate;
use App\Models\Station;
use App\Models\Sentence;
use App\Models\RemandTrial;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1️⃣ Seed all stations
        $stationsData = [
            ['Nsawam Medium Security Prison', 'Greater Accra', 'Nsawam', 'male'],
            ['Nsawam Female Prison', 'Greater Accra', 'Nsawam', 'female'],
            ['Nsawam Camp Prison', 'Greater Accra', 'Nsawam', 'male'],
            ['Akuse Local Prison', 'Eastern Region', 'Akuse', 'male'],
            ['Akuse Female Prison', 'Eastern Region', 'Akuse', 'female'],
            ['James Camp Prison', 'Greater Accra', 'Accra', 'male'],
            ['Senior Correctional Center', 'Greater Accra', 'Accra', 'male'],
            ['Awutu Camp Prison', 'Central Region', 'Awutu', 'male'],
            ['Winneba Local Prison', 'Central Region', 'Winneba', 'male'],
            ['Osamkrom Camp Prison', 'Central Region', 'Osamkrom', 'male'],
            ['Kumasi Central Prison', 'Ashanti Region', 'Kumasi', 'male'],
            ['Obuasi Local Prison', 'Ashanti Region', 'Obuasi', 'male'],
            ['Forifori Camp Prison', 'Eastern Region', 'Forifori', 'male'],
            ['Ankaful Maximum Security Prison', 'Central Region', 'Ankaful', 'male'],
            ['Ankaful Main Camp Prison', 'Central Region', 'Ankaful', 'male'],
            ['Ankaful Annex Prison', 'Central Region', 'Ankaful', 'male'],
            ['Contagious Diseases Prison, Ankaful (CDP)', 'Central Region', 'Ankaful', 'male'],
            ['Koforidua Local Prison', 'Eastern Region', 'Koforidua', 'male'],
            ['Sekondi Central Prison', 'Western Region', 'Sekondi', 'male'],
            ['Ekuase Camp Prison', 'Western Region', 'Ekuase', 'male'],
            ['Tarkwa Local Prison', 'Western Region', 'Tarkwa', 'male'],
            ['Hiawa Camp Prison', 'Western Region', 'Hiawa', 'male'],
            ['Ho Central Prison', 'Volta Region', 'Ho', 'male'],
            ['Ho Female Prison', 'Volta Region', 'Ho', 'female'],
            ['Kpando Local Prison', 'Volta Region', 'Kpando', 'male'],
            ['Kete Krachi Prison', 'Oti Region', 'Kete Krachi', 'male'],
            ['Kumasi Female Prison', 'Ashanti Region', 'Kumasi', 'female'],
            ['Manhyia Local Prison', 'Ashanti Region', 'Manhyia', 'male'],
            ['Ahinsan Prison', 'Ashanti Region', 'Ahinsan', 'male'],
            ['Amanfrom Prison', 'Ashanti Region', 'Amanfrom', 'male'],
            ['Sunyani Central Prison', 'Bono Region', 'Sunyani', 'male'],
            ['Sunyani Female Prison', 'Bono Region', 'Sunyani', 'female'],
            ['Duayaw Nkwanta Prison', 'Ahafo Region', 'Duayaw Nkwanta', 'male'],
            ['Yeji Prison', 'Bono East Region', 'Yeji', 'male'],
            ['Ejura Camp Prison', 'Ashanti Region', 'Ejura', 'male'],
            ['Tamale Central Prison', 'Northern Region', 'Tamale', 'male'],
            ['Tamale Female Prison', 'Northern Region', 'Tamale', 'female'],
            ['Yendi Local Prison', 'Northern Region', 'Yendi', 'male'],
            ['Bawku Local Prison', 'Upper East Region', 'Bawku', 'male'],
            ['Gambaga Local Prison', 'North East Region', 'Gambaga', 'male'],
            ['Navrongo Prison', 'Upper East Region', 'Navrongo', 'male'],
            ['Salaga Local Prison', 'Savannah Region', 'Salaga', 'male'],
            ['Wa Central Prison', 'Upper West Region', 'Wa', 'male'],
            ['Damango Prison', 'Savannah Region', 'Damango', 'male'],
            ['Kenyasi Camp Prison', 'Ahafo Region', 'Kenyasi', 'male'],
        ];

        $stations = collect($stationsData)->map(function ($data) {
            return Station::create([
                'name' => $data[0],
                'slug' => Str::slug($data[0]),
                'code' => strtoupper(implode('', array_map(fn($word) => substr($word, 0, 2), explode(' ', $data[0])))),
                'category' => $data[3],
                'region' => $data[1],
                'city' => $data[2],
            ]);
        });

        // 2️⃣ Create 3 users: one male station, one female, one admin
        $maleStation = $stations->where('category', 'male')->first();
        $femaleStation = $stations->where('category', 'female')->first();
        $adminStation = $stations->where('category', 'male')->values()->get(2); // arbitrary

        User::factory()->create([
            'name' => 'Ohene',
            'email' => 'ohene@gmail.com',
            'serial_number' => "1111",
            'station_id' => $maleStation->id,
            'user_type' => 'prison_admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        User::factory()->create([
            'name' => 'Ella',
            'email' => 'ella@gmail.com',
            'serial_number' => "1122",
            'station_id' => $femaleStation->id,
            'user_type' => 'prison_admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@gmail.com',
            'serial_number' => "0000",
            'user_type' => 'super_admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        User::factory()->create([
            'name' => 'HQ Admin',
            'email' => 'admin@gmail.com',
            'serial_number' => "1234",
            'user_type' => 'hq_admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        // 3️⃣ For each station, create cells, inmates, and remand/trials
        $stations->each(function ($station) {
            $cells = $station->cells()->saveMany(Cell::factory(30)->make());

            // Seed inmates (only if male or female matches)
            Inmate::factory(100)->make()->each(function ($inmate) use ($station, $cells) {
                $cell = $cells->random();
                $admissionDate = fake()->dateTimeBetween('-2 years', 'now');

                $inmate->station()->associate($station);
                $inmate->cell()->associate($cell);
                $inmate->gender = $station->category;
                $inmate->created_at = $admissionDate;
                $inmate->updated_at = $admissionDate;
                $inmate->save();

                $sentence = Sentence::factory()->make([
                    'date_of_sentence' => fake()->dateTimeBetween($admissionDate, 'now')->format('Y-m-d'),
                ]);
                $inmate->sentences()->save($sentence);
            });

            // Seed remand/trials
            RemandTrial::factory(50)->make()->each(function ($remand) use ($station, $cells) {
                $cell = $cells->random();
                $admissionDate = fake()->dateTimeBetween('-2 years', 'now');

                $remand->station()->associate($station);
                $remand->cell()->associate($cell);
                $remand->gender = $station->category;
                $remand->created_at = $admissionDate;
                $remand->updated_at = $admissionDate;
                $remand->save();
            });
        });
    }
}
