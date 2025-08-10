<?php

namespace App\Models;

use App\Models\Scopes\FacilitiesScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([FacilitiesScope::class])]
class ReAdmission extends Model
{
    protected $fillable = [
        'station_id',
        'remand_trial_id',
        're_admission_date',
    ];

    protected $casts = [
        're_admission_date' => 'date'
    ];

    public function remandTrial()
    {
        return $this->belongsTo(RemandTrial::class);
    }
}
