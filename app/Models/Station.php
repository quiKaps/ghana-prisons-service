<?php

namespace App\Models;

use App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Google\Service\DataTransfer\Resource\Transfers;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
    /** @use HasFactory<\Database\Factories\StationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'category',
        'region',
        'city',
    ];


    /**
     * Get the previous convictions associated with the station.
     */
    public function previousConvictions()
    {
        return $this->hasMany(PreviousConviction::class);
    }
  

    /**
     * Get the inter station transfers associated with the station.
     */
    public function interStationTransfers()
    {
        return $this->hasMany(InterStationTransfer::class);
    }

    /**
     * Get the officers associated with the station.
     */
    public function officers()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the inmates associated with the station.
     */
    public function inmates()
    {
        return $this->hasMany(Inmate::class);
    }

    public function remand(): HasMany
    {
        return $this->hasMany(RemandTrial::class)
            ->where('detention_type', 'remand');
    }

    public function trial(): HasMany
    {
        return $this->hasMany(RemandTrial::class)
            ->where('detention_type', 'trial');
    }

     /**
     * Get the settings associated with the station.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(Settings::class);
    }

    public function transfers(): HasMany
{
    return $this->hasMany(Transfers::class , 'to_station_id','id');
}
}
