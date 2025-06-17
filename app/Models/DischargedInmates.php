<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DischargedInmates extends Model
{
    protected $fillable = [
        'serial_number',
        'inmate_type',
        'full_name',
        'surname',
        'first_name',
        'middle_name',
        'offense',
        'admission_date',
        'age_on_admission',
        'court',
        'sentence',
        'date_sentenced',
        'next_court_date',
        'detention_type',
        'station_id',
        'warrant',
        'warrant_document',
        'photo',
        'fingerprint',
        'signature',
        'police_name',
        'police_station',
        'police_contact',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_contact',
        'discharged_by'
    ];

    public function dischargedSentences(): HasMany
    {
        return $this->hasMany(DischargedSentences::class);
    }
}
