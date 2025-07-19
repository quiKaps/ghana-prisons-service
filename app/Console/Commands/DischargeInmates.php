<?php

namespace App\Console\Commands;

use App\Models\Inmate;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DischargeInmates extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inmates:discharge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discharge inmates who have an EPD that is equal to today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Skip if before 7:30 AM
        if ($now->lt($now->copy()->setTime(7, 30))) {
            $this->info("Too early to run.");
            return;
        }

        // Skip if already run today
        $lastRun = Cache::get('inmates_discharge_last_success');
        if ($lastRun && Carbon::parse($lastRun)->isSameDay($now)) {
            $this->info("Already ran today.");
            return;
        }

        $today = $now->toDateString();
        $inmates = Inmate::where('is_discharged', false)->get();

        $dischargedCount = 0;

        foreach ($inmates as $inmate) {
            $latestEpd = $inmate->sentences()->max('epd');

            //Discharge inmate if his latest epd is today
            if ($latestEpd && Carbon::parse($latestEpd)->isSameDay($today)) {

                //Check if this inmate has been already been discharged today
                $existingDischarge = $inmate->discharge()
                    ->whereDate('discharge_date', today())
                    ->exists();

                //Discharge inmate if he hast been dischaged already
                if (!$existingDischarge) {

                    $inmate->is_discharged = true;
                $inmate->save();

                $inmate->discharge()->create([
                    'station_id' => $inmate->station_id,
                    'inmate_id' => $inmate->id,
                    'discharge_type' => 'one-third remission',
                    'discharge_date' => today(),
                ]);

                    $dischargedCount++;
                }
            }
        }

        $this->info("✅ Discharged $dischargedCount inmate(s).");

        // Save today's successful run
        Cache::put('inmates_discharge_last_success', $now->toDateTimeString());
    }
}
