<?php

namespace App\Filament\Station\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Forms\Components\TextInput;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.station.pages.settings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                TextInput::make('slug'),

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('Restore Backrup')
                ->action(function () {
                    Artisan::call('backup:restore', [
                        '--source' => 'local',
                        '--disable-notifications' => true,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Backup Restored')
                        ->body('The backup was restored successfully.')
                        ->send();
                })
                ->requiresConfirmation()
                ->color('danger'),

            Action::make('Create Backup')
                ->action(function () {

                    try {

                        Artisan::call('backup:run');

                    // Get the latest backup file path
                    //$disk = config('backup.backup.destination.disks')[0] ?? 'local';
                    $backupPath = storage_path('app/private/GPSPortal/');
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
