<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Trial;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use PhpParser\Node\Stmt\TryCatch;
use App\Services\DischargeService;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Station\Resources\TrialResource\Pages;
use App\Filament\Station\Resources\TrialResource\Pages\EditTrial;
use App\Filament\Station\Resources\TrialResource\Pages\ListTrials;
use App\Filament\Station\Resources\TrialResource\RelationManagers;
use App\Filament\Station\Resources\TrialResource\Pages\CreateTrial;

class TrialResource extends Resource
{
    protected static ?string $navigationLabel = 'All Trials';

    protected static ?string $title = "Prisoners On Trial";

    protected ?string $subheading = 'Manage and track prisoners currently on trial';

    protected static ?string $model = RemandTrial::class;

    protected static ?string $navigationGroup = 'Remand and Trials';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No prisoner On Trial Found')
            ->emptyStateDescription('Station has no prisoners on trial yet...')
            ->emptyStateIcon('heroicon-s-user')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                    ->label("Prisoner's Name"),
                TextColumn::make('offense')
                    ->badge()
                    ->label('Offense'),
                TextColumn::make('admission_date')
                    ->label('Admitted On')
                    ->date(),
                TextColumn::make('next_court_date')
                    ->label('Next Court Date')
                    ->badge()
                ->color(
                    fn($state) =>
                    Carbon::parse($state)->isFuture() ? 'success' : 'danger'
                )
                    ->date(),
                TextColumn::make('court')
                    ->label('Court of Committal'),
            ])
            ->filters([
                // Define any filters here if needed
            ])
            ->actions([
                //Discharge Action
                Action::make('Discharge')
                    ->color('green')
                ->hidden(fn(RemandTrial $record) => $record->is_discharged)
                ->button()
                    ->icon('heroicon-m-arrow-right-start-on-rectangle')
                    ->modalHeading('Trial Discharge')
                    ->modalSubmitActionLabel('Discharge Prisoner')
                    ->action(function (array $data, RemandTrial $record) {
                try {
                    \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                        $record->update([
                            'is_discharged' => true,
                            'mode_of_discharge' => $data['mode_of_discharge'],
                            'discharged_by' => Auth::id(),
                            'date_of_discharge' => $data['date_of_discharge'],
                        ]);

                        $record->discharge()->create([
                            'station_id' => $record->station_id,
                            'remand_trial_id' => $record->id,
                            'prisoner_type' => $record->detention_type,
                            'discharge_date' => $data['date_of_discharge'],
                            'mode_of_discharge' => $data['mode_of_discharge'],
                            'discharged_by' => Auth::id(),
                        ]);
                    });
                            Notification::make()
                                ->success()
                                ->title('Prisoner Discharged')
                                ->body("{$record->full_name} has been discharged successfully.")
                                ->send();
                        } catch (\Throwable $e) {

                    Notification::make()
                        ->error()
                        ->title('Error Discharging Prisoner')
                        ->body("Discharge failed with error {$e}")
                        ->send();
                }
                    })
                    ->label('Discharge')
                    ->fillForm(fn(RemandTrial $record): array => [
                        'serial_number' => $record->serial_number,
                        'full_name' => $record->full_name,
                        'admission_date' => date_format($record->admission_date, 'Y-m-d'),
                        'offense' => $record->offense,
                        'court' => $record->court,
                        'next_court_date' => date_format($record->next_court_date, 'Y-m-d'),
                    ])
                    ->form([
                        Group::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make("serial_number")
                                    ->label('Serial Number')
                                    ->readOnly(),
                                TextInput::make("full_name")
                                    ->label("Prisoner's Name")
                                    ->readOnly(),
                                TextInput::make('offense')
                                    ->label('Offense')
                                    ->readOnly(),
                                TextInput::make('admission_date')
                                    ->label('Date of Admission')
                                    ->readOnly(),
                                TextInput::make('court')
                                    ->label('Court of Committal')
                                    ->readOnly(),
                                TextInput::make('next_court_date')
                                    ->label('Next Court Date')
                                    ->readOnly(),
                            ]),
                        Section::make('Discharge Details')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('date_of_discharge')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->placeholder('e.g. 2023-12-31')
                                    ->label('Date of Discharge'),
                                Select::make('mode_of_discharge')
                                    ->required()
                                    ->options([
                                        'discharged' => 'Discharged',
                                        'acquitted_and_discharged' => 'Acquitted and Discharged',
                                        'bail_bond' => 'Bail Bond',
                                        'escape' => 'Escape',
                                        'death' => 'Death',
                                        'others' => 'Others',
                                    ])
                                    ->label('Mode of Discharge'),
                            ])->columns(2),
                    ]),
            //Discharge ends

            //readmit as trial starts

            Action::make('Re-Admit')
                ->icon('heroicon-s-arrow-uturn-left')
                ->color('info')
                ->visible(fn(RemandTrial $record) => $record->mode_of_discharge == 'escape' && $record->is_discharged)
                ->button()
                ->action(function ($record) {
                    try {
                        $record->update([
                            'is_discharged' => false,
                        ]);
                        Notification::make()
                            ->success()
                            ->title('Readmission Successfull')
                            ->body("{$record->full_name} has been readmitted on trial successfully.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->error()
                            ->title('Error')
                            ->body("Readmission unsuccessfully with error: {$e}")
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Re-admit this inmate?')
                ->modalSubmitActionLabel('Readmit Escapee'),

            //readmit as trial ends

            //readmission starts
            Action::make('admit_as_convict')
                ->label('Admit as Convict')
                ->icon('heroicon-s-arrow-path')
                ->color('info')
                ->visible(fn(RemandTrial $record) => !$record->is_discharged)
                ->button()
                    ->action(function ($record) {
                        session(['remand_id' => $record->id]);
                        return redirect()->route('filament.station.resources.inmates.create');
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Re-admit this inmate?')
                    ->modalSubmitActionLabel('Proceed to Admission'),
                //readmission ends

                //profile starts
                Action::make('Profile')
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->label('Profile')
                    ->button()
                    ->color('blue')
                    ->url(fn(RemandTrial $record) => route('filament.station.resources.remand-trials.view', [
                        'record' => $record->getKey(),
                    ])),

                //profile ends
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrials::route('/'),
            'create' => Pages\CreateTrial::route('/create'),
            'edit' => Pages\EditTrial::route('/{record}/edit'),
        ];
    }

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
