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
                'surname' => $inmate->surname ?? null,
                'first_name' => $inmate->first_name ?? null,
                'middle_name' => $inmate->middle_name ?? null,
                'full_name' => $inmate->name ?? null,
                'inmate_type' => $inmate instanceof Inmate ? 'convict' : $inmate->detention_type,
                'station_id' => $inmate->station_id,
                'cell_id' => $inmate->cell_id,
                'admission_date' => $inmate->admission_date,
                'discharged_by' => auth()->user()->id,
            ]);

            // Archive sentences if inmate is a convict
            if ($inmate instanceof Inmate && $inmate->sentences) {
                foreach ($inmate->sentences as $sentence) {
                    DischargedSentences::create([
                        'discharged_inmate_id' => $discharged->id,
                        'offense' => $sentence->offense,
                        'sentence' => $sentence->value,
                        'admission_date' => $inmate->admission_date,
                        'date_sentenced' => $sentence->date_sentenced,
                        'court_of_committal' => $sentence->court,
                        'cell_id' => $inmate->cell_id,
                        'EPD' => $sentence->EPD,
                        'LPD' => $sentence->LPD,
                    ]);
                }
            }

            $inmate->delete();
            // or use softDelete() if applicable
        });
    }
}
