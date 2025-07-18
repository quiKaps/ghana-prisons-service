<?php

namespace App\Jobs;

use App\Models\Inmate;
use App\Services\DischargeService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckInmateDischarge implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Inmate $inmate;

    /**
     * Create a new job instance.
     */
    public function __construct(Inmate $inmate)
    {
        $this->inmate = $inmate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(DischargeService::class)->checkAndDischarge($this->inmate);
    }
}
