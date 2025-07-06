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
        'admission_date',
        'age_on_admission',
        'cell_id',
        'date_transferred_in',
        'disability',
        'disability_type',
        'distinctive_marks',
        'distinctive_marks_other',
        'education_level',
        'full_name',
        'gender',
        'goaler',
        'goaler_document',
        'hometown',
        'is_discharged',
        'languages_spoken',
        'married_status',
        'nationality',
        'next_of_kin_contact',
        'next_of_kin_name',
        'next_of_kin_relationship',
        'occupation',
        'religion',
        'part_of_the_body',
        'police_contact',
        'police_name',
        'police_station',
        'previous_convictions',
        'previous_offence',
        'previous_sentence',
        'previous_station_id',
        'previously_convicted',
        'prisoner_picture',
        'religion',
        'serial_number',
        'station_id',
        'station_transferred_from_id',
        'transferred_in',
    ];

    protected $casts = [
        'distinctive_marks' => 'array',
        'languages_spoken' => 'array',
        'disability_type' => 'array',
        'disability' => 'boolean',
        'admission_date' => 'date',
        'date_sentenced' => 'date',
        'goaler_document' => 'array',
        'previous_convictions' => 'array',
    ];


    /**
     * Scope a query to only include inmates scheduled for discharge today or later.
     */
    public function scopeScheduledForDischargeTomorrow($query)
    {
        return $query->whereDate('lpd', now()->addDay()->toDateString());
    }

    /**
     * Scope a query to only include inmates discharged.
     */
    public function scopeDischarged($query)
    {
        return $query->where('is_discharged', true);
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

    public function latestSentenceByDate()
    {
        return $this->hasOne(Sentence::class)->latestOfMany('created_at');
    }



    // /**
    //  * Get the inter cell transfers associated with the inmate.
    //  */
    // public function interCellTransfers(): \Illuminate\Database\Eloquent\Relations\HasMany
    // {
    //     return $this->hasMany(InterCellTransfer::class);
    // }

    /**
     * Get the inter station transfers associated with the inmate.
     */
    public function transfers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * Check if the inmate was transferred out to a specific station.
     *
     * @param int $stationId
     * @return bool
     */
    public function wasTransferredOut($stationId)
    {
        return $this->transfers()->where('from_station_id', $stationId)->exists();
    }

    /**
     * Check if the inmate was transferred in from a specific station.
     *
     * @param int $stationId
     * @return bool
     */
    public function wasTransferredIn($stationId)
    {
        return $this->transfers()->where('to_station_id', $stationId)->exists();
    }

    public function isDischarged(): bool
    {
        return $this->is_discharged;
    }

    public function discharge(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Discharge::class);
    }
}
