<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReAdmission extends Model
{
    protected $fillable = [
        'station_id',
        'remand_trial_id',
        're_admission_date',
    ];

    protected $casts = [
        're_admission_date' => 'date'
    ];

    public function remandTrial()
    {
        return $this->belongsTo(RemandTrial::class);
    }
}
