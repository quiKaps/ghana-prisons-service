<?php

namespace App\Services;

use App\Models\Inmate;
use App\Models\RemandTrial;
use App\Models\DischargedInmates;
use Illuminate\Support\Facades\DB;
use App\Models\DischargedSentences;

class DischargeService
{
    public function dischargeInmate(Inmate|RemandTrial $inmate, array $data): void
    {

        DB::transaction(function () use ($inmate, $data) {
            $discharged = DischargedInmates::create([
                'serial_number' => $inmate->serial_number,
                'full_name' => $inmate->full_name,
                'country_of_origin' => $inmate->country_of_origin,
                'inmate_type' => $inmate instanceof Inmate ? 'convict' : $inmate->detention_type,
                'station_id' => $inmate->station_id,
                'cell_id' => $inmate->cell_id,
                'admission_date' => $inmate->admission_date,
                'discharged_by' => auth()->user()->id,
                'offense' => $inmate->offense ?? null,
                'age_on_admission' => $inmate->age_on_admission ?? null,
                'court' => $inmate->court ?? null,
                'sentence' => $inmate->sentence ?? null,
                'date_sentenced' => $inmate->date_sentenced ?? null,
                'next_court_date' => $inmate->next_court_date ?? null,
                'detention_type' => $inmate->detention_type ?? null,
                'warrant' => $inmate->warrant ?? null,
                'warrant_document' => $inmate->warrant_document ?? null,
                'photo' => $inmate->photo ?? null,
                'fingerprint' => $inmate->fingerprint ?? null,
                'signature' => $inmate->signature ?? null,
                'police_name' => $inmate->police_officer ?? null,
                'police_station' => $inmate->police_station ?? null,
                'police_contact' => $inmate->police_contact ?? null,
                'next_of_kin_name' => $inmate->next_of_kin_name ?? null,
                'next_of_kin_relationship' => $inmate->next_of_kin_relationship ?? null,
                'next_of_kin_contact' => $inmate->next_of_kin_contact ?? null,
                'date_of_discharge' => $data['date_of_discharge'],
                'mode_of_discharge' => $data['mode_of_discharge'],
            ]);

            // Archive sentences if inmate is a convict
            // if ($inmate instanceof Inmate && $inmate->sentences) {
            //     foreach ($inmate->sentences as $sentence) {
            //         DischargedSentences::create([
            //             'discharged_inmate_id' => $discharged->id,
            //             'offense' => $sentence->offense,
            //             'sentence' => $sentence->value,
            //             'admission_date' => $inmate->admission_date,
            //             'date_sentenced' => $sentence->date_sentenced,
            //             'court_of_committal' => $sentence->court,
            //             'cell_id' => $inmate->cell_id,
            //             'EPD' => $sentence->EPD,
            //             'LPD' => $sentence->LPD,
            //         ]);
            //     }
            // }

            $inmate->delete();
            // or use softDelete() if applicable
        });
    }
}
