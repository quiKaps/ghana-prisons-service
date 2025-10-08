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
        'total_sentence',
        'reduced_sentence',
        'offence',
        'EPD',
        'LPD',
        'court_of_committal',
        'commutted_by',
        'commutted_sentence',
        'date_of_sentence',
        // 'goaler_document',
        'sentence_description',
        'date_of_amnesty',
        'amnesty_document',
        'warrant_document',
    ];


    protected $casts = [
        'EPD' => 'date',
        'LPD' => 'date',
        'date_of_sentence' => 'date',
        'date_of_amnesty' => 'date',
    ];


    //Convicts and Sentences

    public function inmate(): BelongsTo
    {
        return $this->belongsTo(Inmate::class);
    }
}
