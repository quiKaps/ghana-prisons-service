<?php

namespace App\Filament\Station\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Facades\Artisan;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TimePicker;
use App\Models\Settings as ModelsSettings;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class Settings extends Page implements HasForms
{
    private $user_station;

        use InteractsWithForms;

         public ?array $data = []; 

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.station.pages.settings';

      public function mount(): void 
    {
        if (Auth::check()) {
           $this->form->fill(Auth::user()->station?->settings?->attributesToArray());
        }
    }

    public function form(Form $form): Form
    {
        return $form
        ->statePath('data')
            ->schema([
              Section::make('App Settings')
    ->description('Manage your application backup seetings')
    ->schema([
       TimePicker::make('backup_time')
       ->seconds(false)
       ->default(env('BACKUP_TIME'))
       ->prefix('Automatically create a backup at')
       ->suffix('every day')
           ->helperText(new HtmlString('<strong>Your system has to be online for the backup to be created.</strong>'))

       ->label('Automatic Backup Time'),
    ]),
    Group::make()
    ->columns(3)   
    ->schema([
      Section::make([
        Checkbox::make('google_drive_backup')
        ->default(Auth::user()->station?->settings?->google_drive_backup)
        ->label('Enable Google Drive Backup'),
    TextInput::make('google_drive_client_id')
    ->disabled()
    ->placeholder('Enter Google Drive Client ID')
    ->label('Google Drive Client ID'),
    TextInput::make('google_drive_client_secret')
     ->disabled()
     ->revealable()
     ->password()
    ->placeholder('Enter Google Drive Client Secret')
    ->label('Google Drive Client Secret'),
    TextInput::make('google_drive_refresh_token')
     ->disabled()
     ->revealable()
     ->password()
    ->placeholder('Enter Google Drive Refresh Token')
    ->label('Google Drive Refresh Token'),
    TextInput::make('google_drive_folder')
     ->disabled()
    ->placeholder('Enter Google Drive Folder')
    ->label('Google Drive Folder')
      ]),
      Section::make([
        Checkbox::make('dropbox_backup')
        ->default(Auth::user()->station?->settings?->dropbox_backup)
        ->label('Enable Dropbox Backup'),
      TextInput::make('dropbox_authorization_token')
    ->revealable()
    ->password()
    ->placeholder('Enter Dropbox Authorization Token')
    ->label('Dropbox Authorization Token'),
    TextInput::make('dropbox_folder')
    ->placeholder('Enter Dropbox Folder')
    ->label('Dropbox Folder')
      ]),
            ])

            ])
        ;

    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

     public function save(): void
    {
        try {
            $data = $this->form->getState();
              $data['station_id'] = Auth::user()->station->id;

            $station_settings = ModelsSettings::where('station_id', Auth::user()->station->id)->first();

            if ($station_settings) {
                $station_settings->update($data);
            } else {
                $station_settings = ModelsSettings::create($data);
            }

            Notification::make()
                ->success()
                ->title('Settings Saved')
                ->body('Your settings have been saved successfully.')
            ->send(); 

        } catch (Halt $exception) {
            Notification::make()
                ->danger()
                ->title('Settings Not Saved')
                ->body('There was an error saving your settings.'. $exception->getMessage())
                ->send();
            return;
        }

        
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('Restore Backup')
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

                          $exitCode = Artisan::call('backup:run');

                    // Get the latest backup file path
                    $disk = config('backup.backup.destination.disks')[0] ?? 'local';
                    $backupPath = storage_path('app/private/GPSPortal/');
                        $files = collect(glob($backupPath . '*.zip'))->sortByDesc(fn($file) => filemtime($file));
                        $latestBackup = $files->first();

                        if (($latestBackup && file_exists($latestBackup)) && $exitCode === 0) {
                            Notification::make()
                                ->success()
                                ->title('Backup Created')
                                ->body('The backup was created successfully.')
                                ->send();

                                return redirect()->to(route('filament.station.pages.backups'));
                       }
                       else {
                            Notification::make()
                                ->danger()
                                ->title('Backup Not Found')
                               ->body('Backup wasn\'t created successfully. Please check if you have internet access or configuration is correct.')
                                ->send();
                        }
                    } catch (\Throwable $e) {

                        Notification::make()
                            ->danger()
                            ->title('Backup Failed')
                            ->body('An error occurred while creating the backup: ' . $e->getMessage())
                            ->send();

                        // Return null or throw exception to prevent further processing
                        throw $e;
                    }
                }),
        ];
    }
}
