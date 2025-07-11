<?php

namespace App\Models;

use App\Models\Scopes\FacilitiesScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
        'mode_of_discharge',
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
        'serial_number',
        'station_id',
        'date_transferred_in',
        'station_transferred_from_id',
        'transferred_in',
        'transferred_out',
        'date_transferred_out',
        'station_transferred_to_id',
        'court_of_committal',
        'tribe',
        'date_of_discharge'
    ];


    protected $casts = [
        'distinctive_marks' => 'array',
        'languages_spoken' => 'array',
        'disability_type' => 'array',
        'goaler_document' => 'array',
        'previous_convictions' => 'array',

        'disability' => 'boolean',
        'is_discharged' => 'boolean',
        'transferred_in' => 'boolean',
        'transferred_out' => 'boolean',
        'goaler' => 'boolean',
        'previously_convicted' => 'boolean',

        'admission_date' => 'date',
        'date_of_discharge' => 'date',
        'date_transferred_in' => 'date',
        'date_transferred_out' => 'date',
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



    public function station(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Station::class);
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

    public function earliestSentenceByDate()
    {
        return $this->hasOne(Sentence::class);
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


    //scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('transferred_out', false)
            ->where('is_discharged', false);
    }


    public function scopeRecidivists(Builder $query): Builder
    {
        return $query->where('transferred_out', false)
            ->where('is_discharged', false)
            ->where('previously_convicted', true);
    }

    public function scopeConvictOnTrial(Builder $query): Builder
    {
        return $query->where('transferred_out', false)
            ->where('is_discharged', false)
            ->where('goaler', true);
    }

    public function scopeDischargedToday(Builder $query): Builder
    {
        return $query->scopeDischarged()->where('transferred_out', false)
            ->where('goaler', true);
    }

    public function scopeWithSentenceType(Builder $query, string $field, string|array $value, bool $negate = false): Builder
    {
        return $query->where('transferred_out', false)->where('is_discharged', false)
            ->whereHas('latestSentenceByDate', function ($q) use ($field, $value, $negate) {
                if ($negate) {
                    $q->whereNotIn($field, (array) $value);
                } else {
                    $q->whereIn($field, (array) $value);
                }
            });
    }

    public function scopeWithoutOffences($query): Builder
    {
        $excluded = ['death', 'manslaughter', 'murder', 'robbery', 'life'];

        return $query->whereHas('sentences', function ($q) use ($excluded) {
            $q->whereNotIn('offence', $excluded);
        });
    }

    public function scopeEscapees(Builder $query): Builder
    {
        return $query
            ->where('is_discharged', true)
            ->where('mode_of_discharge', 'escape')
            ->whereHas('discharge', function ($q) {
                $q->where('mode_of_discharge', 'escape');
            });
    }

    public function latestSentenceByEpd()
    {
        return $this->hasOne(Sentence::class)->latestOfMany('epd');
    }

    public function scopeWithEpdThisMonth($query)
    {
        return $query->whereHas('latestSentenceByDate', function ($subQuery) {
            $subQuery->whereBetween('epd', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        });
    }


    protected static function booted(): void
    {
        static::addGlobalScope('latest', function ($query) {
            $query->latest(); // or ->orderBy('created_at', 'desc')
        });
    }
}
