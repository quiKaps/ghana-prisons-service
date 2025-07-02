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
        'age_on_admission',
        'admission_date',
        'date_sentenced',
        'offence',
        'other_offence',
        'sentence',
        'EPD',
        'LPD',
        'court_of_committal',
        'cell_id',
        'station_id',
        'prisoner_picture',
        'warrant_document',
        'transferred_in',
        'station_transferred_from_id',
        'date_transferred_in',
        'disability_type_other',
        'tribe',
        'languages_spoken',
        'hometown',
        'nationality',
        'married_status',
        'education_level',
        'religion',
        'occupation',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'next_of_kin_contact',
        'distinctive_marks',
        'distinctive_marks_other',
        'part_of_the_body',
        'goaler',
        'goaler_document',
        'previously_convicted',
        'previous_sentence',
        'previous_offence',
        'previous_station_id',
        'police_name',
        'police_station',
        'police_contact',
        'station_id',
        'gender',
    ];

    protected $casts = [
        'distinctive_marks' => 'array',
        'languages_spoken' => 'array',
        'disability' => 'boolean',
        'admission_date' => 'date',
        'date_sentenced' => 'date',
    ];


    /**
     * Scope a query to only include inmates scheduled for discharge today or later.
     */
    public function scopeScheduledForDischargeTomorrow($query)
    {
        return $query->whereDate('lpd', now()->addDay()->toDateString());
    }

    /**
     * Scope a query to only include inmates scheduled for discharge today or
     */
    public function scopeScheduledForDischargeToday($query)
    {
        return $query->whereDate('lpd', now()->toDateString());
    }

    public function scopeScheduledDischargePassed($query)
    {
        return $query->whereDate('lpd', '<', now()->toDateString());
    }


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
