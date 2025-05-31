<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inmate extends Model
{
    /** @use HasFactory<\Database\Factories\InmateFactory> */
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'surname',
        'first_name',
        'middle_name',
        'gender',
        'married_status',
        'age_on_admission',
        'date_of_birth',
        'offence',
        'sentence',
        'admission_date',
        'date_sentenced',
        'previously_convicted',
        'previous_conviction_id',
        'cell_id',
        'court_of_committal',
        'EPD',
        'LPD',
        'photo',
        'fingerprint',
        'signature',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_contact',
        'medical_conditions',
        'allergies',
        'religion',
        'nationality',
        'education_level',
        'occupation',
        'hometown',
        'tribe',
        'distinctive_marks',
        'languages_spoken',
        'disability',
        'disability_type',
        'police_name',
        'police_station',
        'police_contact',
        'goaler',
        'goaler_document',
        'warrant_document',

    ];

    /**
     * Get the cell associated with the inmate.
     */
    public function cell(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cell::class);
    }

    /**
     * Get all the sentences associated with the inmate.
     */

    public function sentences(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sentence::class);
    }

    /**
     * Get the inter cell transfers associated with the inmate.
     */
    public function interCellTransfers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InterCellTransfer::class);
    }

    /**
     * Get the inter station transfers associated with the inmate.
     */
    public function interStationTransfers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InterStationTransfer::class);
    }
}
