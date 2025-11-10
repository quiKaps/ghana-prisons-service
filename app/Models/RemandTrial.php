<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\FacilitiesScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([FacilitiesScope::class])]

class RemandTrial extends Model
{
    /** @use HasFactory<\Database\Factories\RemandTrialFactory> */
    use HasFactory;

    const TYPE_REMAND = 'remand';
    const TYPE_TRIAL = 'trial';


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
        'picture',
        'mode_of_discharge',
        'is_discharged',
        'date_of_discharge',
        'discharged_by',
    ];


    protected $casts = [
        'admission_date' => 'date',
        're_admission_date' => 'date',
        'next_court_date' => 'date',
        'date_of_discharge' => 'date',
        'is_discharged' => 'boolean',
    ];




    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function reAdmissions(): HasMany
    {
        return $this->hasMany(ReAdmission::class);
    }

    public function discharge(): HasMany
    {
        return $this->hasMany(RemandTrialDischarge::class);
    }

    //Scopes

    public function scopeRemand($query)
    {
        return $query->where('detention_type', 'remand')->where('is_discharged', false);
    }

    public function scopeTrial($query)
    {
        return $query->where('detention_type', 'trial')->where('is_discharged', false);
    }

    public function scopeActive(Builder $query, string $type): Builder
    {
        return $query
            ->where('detention_type', $type)
            ->where('is_discharged', false)
            ->whereDate('next_court_date', '>=', Carbon::today());
    }

    public function scopeForeigners(Builder $query, string $type): Builder
    {
        return $query
            ->where('detention_type', $type)
            ->where('is_discharged', false)
           ->whereNotIn(DB::raw('LOWER(country_of_origin)'), ['ghana', 'ghanaian']);
    }

    public function scopeExpiredWarrants(Builder $query, string $type): Builder
    {
        return $query
            ->where('detention_type', $type)
            ->where('is_discharged', false)
            ->whereNotNull('next_court_date')
            ->whereDate('next_court_date', '<', Carbon::today());
    }

    public function scopeEscapees(Builder $query, string $type): Builder
    {
        return $query
            ->where('detention_type', $type)
            ->where('is_discharged', true)
            ->where('mode_of_discharge', 'escape');
    }

    public function scopeDischarged(Builder $query, string $type): Builder
    {
        return $query
            ->where('detention_type', $type)
            ->where('is_discharged', true)
            ->where('mode_of_discharge', '!=', 'escape');
    }

    public function createdToday(Builder $query)
    {
        return $query->whereDate('created_at', today());
    }

    protected static function booted(): void
    {
        static::addGlobalScope('latest', function ($query) {
            $query->latest(); // or ->orderBy('created_at', 'desc')
        });
    }
}
