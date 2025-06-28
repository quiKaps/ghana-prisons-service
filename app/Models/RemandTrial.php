<?php

namespace App\Models;

use App\Models\Scopes\FacilitiesScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([FacilitiesScope::class])]

class RemandTrial extends Model
{
    /** @use HasFactory<\Database\Factories\RemandTrialFactory> */
    use HasFactory;

    protected $fillable = [
        'station_id',
        'cell_id',
        'serial_number',
        'full_name',
        'offense',
        'gender',
        'admission_date',
        'age_on_admission',
        'court',
        'detention_type',
        'next_court_date',
        'warrant',
        'country_of_origin',
        'police_station',
        'police_officer',
        'police_contact',
        're_admission_date',
        'warrant',
        'picture'
    ];
    protected $casts = [
        'admission_date' => 'date',
        'next_court_date' => 'date',
        'age_on_admission' => 'integer',
    ];


    /**
     * Get the cell associated with the inmate.
     */
    public function cell(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cell::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
