<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Get the cells associated with the station.
     */
    public function cells()
    {
        return $this->hasMany(Cell::class);
    }
    /**
     * Get the previous convictions associated with the station.
     */
    public function previousConvictions()
    {
        return $this->hasMany(PreviousConviction::class);
    }
    /**
     * Get the inter cell transfers associated with the station.
     */
    public function interCellTransfers()
    {
        return $this->hasMany(InterCellTransfer::class);
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
}
