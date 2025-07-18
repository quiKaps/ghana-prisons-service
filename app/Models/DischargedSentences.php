<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DischargedSentences extends Model
{
    protected $fillable = [
        'discharged_inmate_id',
        'offense',
        'sentence',
        'admission_date',
        'date_sentenced',
        'court_of_committal',
        'EPD',
        'LPD'
    ];


    public function dischargedInmate(): BelongsTo
    {
        return $this->belongsTo(DischargedInmates::class);
    }
}
