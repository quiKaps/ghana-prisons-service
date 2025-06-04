<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $stations = [
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

        foreach ($stations as $station) {
            Station::create([
                'name' => $station[0],
                'slug' => Str::slug($station[0]),
                'code' => strtoupper(implode('', array_map(fn($word) => substr($word, 0, 2), explode(' ', $station[0])))),
                'category' => $station[3],
                'region' => $station[1],
                'city' => $station[2],
            ]);
        }
    }
}
