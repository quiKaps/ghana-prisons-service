<?php

namespace App\Services;

use App\Models\Inmate;
use App\Models\RemandTrial;
use Illuminate\Support\Carbon;
use App\Models\DischargedInmates;
use Illuminate\Support\Facades\DB;
use App\Models\DischargedSentences;

class DischargeService
{
    public function checkAndDischarge(Inmate $inmate): void
    {
        $latestEPD = $inmate->sentences()->latest('epd')->value('epd');

        if ($latestEPD && Carbon::parse($latestEPD)->isToday()) {

            $inmate->updateQuietly(['is_discharged' => true]);

            \App\Models\Discharge::create([
                'station_id' => $inmate->station_id,
                'inmate_id' => $inmate->id,
                'discharge_type' => 'one-third remission',
                'discharge_date' => today(),
                //'reason' => $data['reason'],
            ]);
        }
    }
}
