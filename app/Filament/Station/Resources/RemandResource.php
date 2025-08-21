<?php

namespace App\Filament\Station\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Remand;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ReAdmission;
use App\Models\RemandTrial;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
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
use App\Filament\Station\Resources\RemandResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Station\Resources\RemandResource\Pages\EditRemand;
use App\Filament\Station\Resources\RemandResource\RelationManagers;
use App\Filament\Station\Resources\RemandResource\Pages\ListRemands;
use App\Filament\Station\Resources\RemandResource\Pages\CreateRemand;

class RemandResource extends Resource
{
    protected static ?string $navigationGroup = 'Remand and Trials';

    protected static ?string $navigationLabel = 'All Remands';

    protected static ?string $title = 'Prisoners On Remand';

    protected ?string $subheading = "View and manage remand prisoners";

    protected static ?string $model = RemandTrial::class;

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
            // ->query(RemandTrial::query()
            //     ->where('detention_type', 'remand')
            //     ->where('next_court_date', '>=', now())
            //     ->where('is_discharged', false)
            //     ->orderBy('created_at', 'DESC'))
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                    ->label("Name of Prisoner"),
                TextColumn::make('country_of_origin')
                    ->badge()
                    ->label('Nationality'),
                TextColumn::make('offense')
                    ->badge()
                    ->searchable()
                    ->label('Offence'),
                TextColumn::make('admission_date')
                    ->date()
                    ->searchable()
                    ->label('Date of Admission'),
                TextColumn::make('next_court_date')
                    ->badge()
                    ->color(
                        fn($state) =>
                        Carbon::parse($state)->isFuture() ? 'success' : 'danger'
                    )
                    ->label('Next Court Date')
                    ->date(),
                TextColumn::make('court')
                    ->searchable()
                    ->label('Court of Committal'),
            ])
            ->filters([
                // Define any filters here if needed
            ])

            ->actions([

            //dischrge start
            Action::make('Discharge')
                    ->color('green')
                ->hidden(fn(RemandTrial $record) => $record->is_discharged)
                ->button()
                ->icon('heroicon-m-arrow-right-start-on-rectangle')
                ->modalHeading('Remand Discharge')
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
                            ->success()
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
                                    'escape' => 'Escape',
                                    'death' => 'Death',
                                    'others' => 'Others',
                                ])
                                ->label('Mode of Discharge'),
                        ])->columns(2),
                ]),

            //discharge ends here
            Action::make('readmit')
                ->color('info')
                ->visible(fn(RemandTrial $record) => $record->is_discharged)
                ->button()
                ->label('Re-Admit')
                ->icon('heroicon-m-arrow-uturn-left')
                ->modalHeading('Re-Admit')
                ->modalSubmitActionLabel('Re-Admit Remand')
                ->action(function (array $data, RemandTrial $record) {
                try {
                    \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                        $record->update([
                            'is_discharged' => false,
                            'mode_of_discharge' => null,
                            'discharged_by' => null,
                            'next_court_date' => $data['next_court_date'],
                            're_admission_date' => now()
                        ]);

                        ReAdmission::create([
                            'station_id' => $record->station_id,
                            'remand_trial_id' => $record->id,
                            're_admission_date' => now(),
                        ]);
                    });

                    Notification::make()
                            ->success()
                            ->title('Prisoner Readmitted')
                            ->body("{$record->full_name} has been readmitted successfully.")
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->success()
                            ->title('Error')
                            ->body("Re-admission failed with error {$e}")
                            ->send();
                    }
                })
                ->fillForm(fn(RemandTrial $record): array => [
                    'serial_number' => $record->serial_number,
                    'full_name' => $record->full_name,
                    'admission_date' => date_format($record->admission_date, 'Y-m-d'),
                    'offense' => $record->offense,
                    'court' => $record->court,
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
                            DatePicker::make('next_court_date')
                                ->label('Next Court Date')
                                ->required(),
                        ]),
                ]),
                Action::make('Profile')
                    ->color('gray')
                    ->icon('heroicon-o-user')
                    ->label('Profile')
                    ->button()
                    ->color('blue')
                    ->url(fn(RemandTrial $record) => route('filament.station.resources.remand-trials.view', [
                        'record' => $record->getKey(),
                    ])),
            ])
            ->headerActions([

                FilamentExportHeaderAction::make('export')
                    ->color('green')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Export Trials')
                    ->fileName(Auth::user()->station->name . ' - ' . now()->format('Y-m-d') . ' - Trials')
                    ->directDownload()
                    ->withColumns([

                        TextColumn::make('station.name')->label('Station'),
                        TextColumn::make('cell.cell_number')
                            ->label('Cell Number - Block')
                            ->getStateUsing(function ($record) {
                                if ($record->cell) {
                                    return "{$record->cell->cell_number} - {$record->cell->block}";
                                }
                                return '';
                            }),
                        TextColumn::make('gender')->label('Gender'),
                        TextColumn::make('age_on_admission')->label('Age on Admission'),
                        TextColumn::make('detention_type')->label('Detention Type'),
                        TextColumn::make('warrant')->label('Warrant'),
                        TextColumn::make('country_of_origin')->label('Country of Origin'),
                        TextColumn::make('police_station')->label('Police Station'),
                        TextColumn::make('police_officer')->label('Police Officer'),
                        TextColumn::make('police_contact')->label('Police Contact'),
                    ])

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
            'index' => Pages\ListRemands::route('/'),
            'create' => Pages\CreateRemand::route('/create'),
            'edit' => Pages\EditRemand::route('/{record}/edit'),
        ];
    }

    //show resource navigation to only prison_admin
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user?->user_type === 'prison_admin';
    }
}
