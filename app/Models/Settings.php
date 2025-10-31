<?php

namespace App\Models;

use App\Models\Station;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settings extends Model
{
    /** @use HasFactory<\Database\Factories\SettingsFactory> */
    use HasFactory;

    protected $fillable = ['station_id', 'backup_time', 'dropbox_backup', 's3_backup', 'google_drive_backup', 'google_drive_client_id', 'google_drive_client_secret', 'google_drive_refresh_token', 'google_drive_folder'];

    protected $casts = [
        'dropbox_backup' => 'boolean',
        's3_backup' => 'boolean',
        'google_drive_backup' => 'boolean',
    ];

    //protected $hidden = ['google_drive_client_secret', 'google_drive_refresh_token'];

     public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
