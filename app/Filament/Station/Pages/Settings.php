<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.station.pages.settings';

    protected function getHeaderActions(): array
    {
        return [

            Action::make('Create Backup')
                ->action(function () {

                    try {

                        Artisan::call('backup:run');

                        // Get the latest backup file path
                        //$disk = config('backup.backup.destination.disks')[0] ?? 'local';
                        $backupPath = '/home/ohene/sites/gps/storage/app/private/GPSPortal/';
                        $files = collect(glob($backupPath . '*.zip'))->sortByDesc(fn($file) => filemtime($file));
                        $latestBackup = $files->first();

                        if ($latestBackup && file_exists($latestBackup)) {
                            Notification::make()
                                ->success()
                                ->title('Backup Created')
                                ->body('The backup was created successfully. Download will start shortly.')
                                ->send();

                            return response()->download($latestBackup)->deleteFileAfterSend(true);
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('Backup Not Found')
                                ->body('Backup was created but the file could not be found.')
                                ->send();
                        }
                    } catch (\Throwable $e) {

                        Notification::make()
                            ->danger()
                            ->title('Print Failed')
                            ->body('An error occurred while generating the PDF: ' . $e->getMessage())
                            ->send();

                        // Return null or throw exception to prevent further processing
                        throw $e;
                    }
                }),
        ];
    }
}
