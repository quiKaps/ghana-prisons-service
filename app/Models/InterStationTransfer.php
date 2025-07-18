<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterStationTransfer extends Model
{
    /** @use HasFactory<\Database\Factories\InterStationTransferFactory> */
    use HasFactory;

    protected $fillable = [
        'inmate_id',
        'from_station_id',
        'to_station_id',
        'reason',
        'officer_in_charge',
        'status',
        'transfer_order_number',
        'transfer_document',
        'remarks',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];
}
