<?php

use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Display all empty cells
Artisan::command('cells:empty', function () {
    $this->comment('All Empty Cells: ' . \App\Models\Cell::whereDoesntHave('inmates')->count());
})->purpose('Display all empty cells');

// Delete all empty cells
Artisan::command('cells:delete', function () {
    $this->comment('Deleting all empty cells...');
    $cells_count = \App\Models\Cell::whereDoesntHave('inmates')->count();
    \App\Models\Cell::whereDoesntHave('inmates')->delete();
    $this->comment($cells_count . ' empty cell(s) have been deleted.');
})->purpose('Delete all empty cells');

// Run inmates:discharge command every 30 minutes
Schedule::command('inmates:discharge')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/discharge.log'));

// Run Commands for Daily Backup
Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at(Settings::first()->backup_time, env('BACKUP_TIME'));
