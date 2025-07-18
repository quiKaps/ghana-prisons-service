<?php

namespace App\Models;

use App\Models\Scopes\FacilitiesScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([FacilitiesScope::class])]
class InmateRemandUnion extends Model
{
    protected $table = 'inmate_remand_union';

    protected $primaryKey = 'id'; // use the synthetic ID
    public $incrementing = false; // still false — not a real auto-increment
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'detention_type' => 'string',
        'admission_date' => 'date'
    ];

    public function station()
    {
        return $this->hasOne(Station::class, 'id', 'station_id');
    }
}
