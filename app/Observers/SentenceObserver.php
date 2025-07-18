<?php

namespace App\Observers;

use App\Models\Sentence;
use App\Jobs\CheckInmateDischarge;

class SentenceObserver
{
    /**
     * Handle the Sentence "created" event.
     */
    public function created(Sentence $sentence)
    {
        CheckInmateDischarge::dispatch($sentence->inmate);
    }

    /**
     * Handle the Sentence "updated" event.
     */
    public function updated(Sentence $sentence)
    {
        CheckInmateDischarge::dispatch($sentence->inmate);
    }
}
