<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemandTrialDischarge extends Model
{
    protected $fillable = [
        'station_id',
        'remand_trial_id',
        'prisoner_type',
        'discharge_date',
        'mode_of_discharge',
        'discharged_by',
    ];

    protected $casts = [
        'discharge_date' => 'date',
    ];

    public function remandTrial(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\RemandTrial::class);
    }
}
