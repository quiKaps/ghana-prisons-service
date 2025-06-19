<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sentence extends Model
{
    /** @use HasFactory<\Database\Factories\SentenceFactory> */
    use HasFactory;

    protected $fillable = [
        'inmate_id',
        'sentence',
        'offence',
        'EPD',
        'LPD',
        'goaler_document',
        'warrant_document',
    ];


    protected $casts = [
        'EPD' => 'date',
        'LPD' => 'date',
    ];


    //Convicts and Sentences

    public function inmate(): BelongsTo
    {
        return $this->belongsTo(Inmate::class);
    }
}
