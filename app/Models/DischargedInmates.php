<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DischargedInmates extends Model
{
    protected $fillable = [
        'serial_number',
        'inmate_type',
        'full_name',
        'country_of_origin',
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
        'mode_of_discharge',
        'date_of_discharge',
        'discharged_by'
    ];

    protected $casts = [
        'admission_date' => 'date',
        'date_sentenced' => 'date',
        'date_of_discharge' => 'date'
    ];

    public function dischargedSentences(): HasMany
    {
        return $this->hasMany(DischargedSentences::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
