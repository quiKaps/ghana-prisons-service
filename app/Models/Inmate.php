<?php

namespace App\Models;

use App\Models\Scopes\FacilitiesScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([FacilitiesScope::class])]
class Inmate extends Model
{
    /** @use HasFactory<\Database\Factories\InmateFactory> */
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'full_name',
        'gender',
        'married_status',
        'age_on_admission',
        'date_of_birth',
        'admission_date',
        'date_sentenced',
        'previously_convicted',
        'previous_conviction_id',
        'cell_id',
        'court_of_committal',
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

    ];

    protected $casts = [
        'medical_conditions' => 'array',
        'allergies' => 'array',
        'languages_spoken' => 'array',
        'disability' => 'boolean',
        'admission_date' => 'date',
        'date_sentenced' => 'date',
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
