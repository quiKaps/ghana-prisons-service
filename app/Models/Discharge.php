<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discharge extends Model
{
    //
    protected $fillable = [
        'inmate_id',
        'discharge_type', // e.g., parole, completion of sentence, medical discharge
        'discharge_date',
        'reason', // Reason for discharge, if applicable
        'discharge_document', // Document related to discharge, if applicable
        'discharged_by', // Officer responsible for discharge
    ];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'discharge_date' => 'date',
    ];

    // Inmate
    public function discharge(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Discharge::class);
    }
}
