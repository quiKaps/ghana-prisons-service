<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\DischargedInmates;
use App\Services\ReadmissionService;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;

class DischargedRemandTrials extends Page implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.station.pages.discharged-remand-trials';

    protected static ?string $navigationLabel = 'Discharged Remand and Trials';

    protected static ?string $title = 'Discharged Remand and Trials';

    protected ?string $subheading = 'Manage and track inmates discharged from trials or remands';

    protected static ?string $model = DischargedInmates::class;

    protected static ?string $navigationGroup = 'Trials';

    public function table(Table $table): Table
    {
        return $table
            ->query(DischargedInmates::query()
                ->where('inmate_type', 'trial')
                ->orWhere('inmate_type', 'remand')
                ->orderBy('created_at', 'DESC'))
            ->emptyStateHeading('Station has no disharged  inmates')
            ->emptyStateIcon('heroicon-s-user')
            ->columns([
                TextColumn::make('serial_number')
                    ->weight(FontWeight::Bold)
                    ->label('S.N.'),
                TextColumn::make('full_name')
                    ->searchable()
                    ->label('Inmate Name'),
                TextColumn::make('admission_date')
                    ->label('Admission Date')
                    ->date(),
                TextColumn::make('court')
                    ->label('Court'),
                TextColumn::make('next_court_date')
                    ->label('Next Court Date')
                    ->badge()
                    ->color('success')
                    ->date(),
                TextColumn::make('police_officer')
                    ->label('Police Officer'),
                TextColumn::make('police_contact')
                    ->label('Police Contact'),
            ])
            ->filters([
                // Define any filters here if needed
            ])
            ->actions([
                Action::make('readmit')
                    ->label('Readmit')
                    ->icon('heroicon-o-arrow-uturn-right')
                    ->color('warning')
                    ->fillForm(fn(DischargedInmates $record): array => [
                        'serial_number' => $record->serial_number,
                        'name' => $record->full_name,
                    ])
                    ->form([
                        TextInput::make('serial_number')
                            ->required(),
                        TextInput::make('prisoner_name')
                            ->label('Prisoner Name')
                            ->required(),
                        DatePicker::make('readmission_date')
                            ->required(),
                        DatePicker::make('next_court_date')
                            ->required(),
                    ])
                    ->modalHeading('Readmit Inmate')
                    ->modalSubmitActionLabel('Confirm Readmission')

                    ->action(function ($data, $record) {
                        app(ReAdmissionService::class)->readmitRemandTrial($record->id, $data);

                        Notification::make()
                            ->success()
                            ->title('Readmission Successful')
                            ->body('The inmate has been re-admitted to remand/trial.')
                            ->send();
                    }),
                Action::make('Re-Admit')
                    ->color('brown')
                    ->button()
                    ->icon('heroicon-m-arrow-right-end-on-rectangle')
                    ->modalHeading('Remand/Trial Re-Admission Form')
                    ->modalSubmitActionLabel('Re-Admit Imate')
                    ->action(function (array $data, $record) {
                        // app(\App\Services\DischargeService::class)
                        //     ->dischargeInmate($record, $data);
                        // Notification::make()
                        //     ->success()
                        //     ->title('Inmate Discharged')
                        //     ->body('The inmates has been discharged successfully.')
                        //     ->send();
                    })
                    ->label('Re-Admit')
                    ->fillForm(fn(DischargedInmates $record): array => [
                        'serial_number' => $record->serial_number,
                        'name' => $record->name,
                        'age_on_admission' => $record->age_on_admission,
                        'country_of_origin' => $record->country_of_origin,
                        'admission_date' => $record->admission_date,
                        'detention_type' => $record->detention_type,
                        'offense' => $record->offense,
                        'court' => $record->court,
                        'police_station' => $record->police_station,
                        'police_officer' => $record->police_officer,
                        'police_contact' => $record->police_contact,

                    ])
                    ->form([
                        Section::make('Inmate Details')
                            ->columns(2)
                            ->schema([
                                TextInput::make('serial_number')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('e.g. NSW/06/25')
                                    ->label('Serial Number'),
                                TextInput::make('name')
                                    ->required()
                                    ->placeholder('e.g. Nana Kwame')
                                    ->label('Inmate Name'),
                                TextInput::make('age_on_admission')
                                    ->numeric()
                                    ->minValue(15)
                                    ->placeholder('e.g. 30')
                                    ->required()
                                    ->label('Age on Admission'),
                                Select::make('country_of_origin')
                                    ->options(config('countries'))
                                    ->searchable()
                                    ->required()
                                    ->label('Country of Origin'),
                                DatePicker::make('admission_date')
                                    ->required()
                                    ->default(now())
                                    ->label('Admission Date'),

                                Select::make('detention_type')
                                    ->options([
                                        'remand' => 'Remand',
                                        'trial' => 'Trial',
                                    ])
                                    ->required()
                                    ->label('Detention Type'),
                            ])->columns(2),
                        Section::make('Legal Details')
                            ->columns(2)
                            ->schema([
                                TextInput::make('offense')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Theft')
                                    ->label('Offense'),
                                TextInput::make('court')
                                    ->required()
                                    ->placeholder('e.g. Kumasi Circuit Court')
                                    ->label('Court'),
                                DatePicker::make('next_court_date')
                                    ->required()
                                    ->label('Next Court Date'),
                                TextInput::make('police_station')
                                    ->required()
                                    ->placeholder('e.g. Central Police Station')
                                    ->label('Police Station'),
                                TextInput::make('police_officer')
                                    ->label('Police Officer')
                                    ->placeholder('e.g. Inspector Kwesi Nyarko'),
                                TextInput::make('police_contact')
                                    ->label('Police Contact')
                                    ->placeholder('e.g. 0241234567')
                                    ->tel(),
                            ]),
                        Section::make('Discharge Details')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('date_of_discharge')
                                    ->required()
                                    ->default(now())
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
                                        'other' => 'Other',
                                    ])
                                    ->label('Mode of Discharge'),
                            ])->columns(2),

                    ]),
            ]);
    }
}
