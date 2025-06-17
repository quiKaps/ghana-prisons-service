<?php

namespace App\Services;

use App\Models\DischargedInmate;
use App\Models\DischargedInmates;
use App\Models\RemandTrial;
use Illuminate\Support\Facades\DB;

class ReadmissionService
{
    public function readmitRemandTrial(int $dischargedInmateId, array $data): RemandTrial
    {
        return DB::transaction(function () use ($dischargedInmateId, $data) {
            $archived = DischargedInmates::findOrFail($dischargedInmateId);

            $remandTrial = RemandTrial::create([
                'station_id' => $archived->station_id,
                'cell_id' => $archived->cell_id,
                'serial_number' => $data['serial_number'],
                'name' => $data['prisoner_name'],
                'offense' => $archived->offense,
                'admission_date' => $data['readmission_date'],
                'age_on_admission' => $archived->age_on_admission,
                'court' => $archived->court,
                'detention_type' => $archived->inmate_type, // 'remand' or 'trial'
                'next_court_date' => $data['next_court_date'],
                'warrant' => $archived->warrant,
                'country_of_origin' => $archived->country_of_origin,
                'police_station' => $archived->police_station,
                'police_officer' => $archived->police_name,
                'police_contact' => $archived->police_contact,
                // add other relevant fields as needed
            ]);

            $archived->delete(); // or soft delete for traceability

            return $remandTrial;
        });
    }
}
