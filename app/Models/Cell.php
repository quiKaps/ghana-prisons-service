<?php

namespace App\Models;

use App\Models\Scopes\FacilitiesScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([FacilitiesScope::class])]

class Cell extends Model
{
    /** @use HasFactory<\Database\Factories\CellFactory> */
    use HasFactory;

    protected $fillable = [
        'cell_number',
        'block',
        'station_id',
    ];

    /**
     * Get the station that owns the cell.
     */
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * Get the inmates associated with the cell.
     */
    public function inmates()
    {
        return $this->hasMany(Inmate::class);
    }
    /**
     * Get the inter cell transfers associated with the cell.
     */
    public function interCellTransfers()
    {
        return $this->hasMany(InterCellTransfer::class);
    }
}
