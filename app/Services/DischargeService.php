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
            // Check if this inmate has been already been discharged today
            $existingDischarge = $inmate->discharge()
                ->whereDate('discharge_date', today())
                ->exists();

            //Discharge inmate if he hast been dischaged already
            if (!$existingDischarge) {
                $inmate->updateQuietly(['is_discharged' => true]);

                $inmate->discharge()->create([
                    'station_id' => $inmate->station_id,
                    'inmate_id' => $inmate->id,
                    'discharge_type' => 'one-third remission',
                    'discharge_date' => today(),
                    //'reason' => $data['reason'],
                ]);
            }
        }
    }
}
