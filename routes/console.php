<?php

use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Display all empty cells

// Delete all empty cells

// Run inmates:discharge command every 30 minutes
Schedule::command('inmates:discharge')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/discharge.log'));

// Run Commands for Daily Backup
Schedule::command('backup:clean')->daily()->at('01:00');

if(Schema::hasTable('settings'))
{
    $backupTime = Settings::value('backup_time') ?? env('BACKUP_TIME', '01:00');
    Schedule::command('backup:run')->daily()->at($backupTime);
}
