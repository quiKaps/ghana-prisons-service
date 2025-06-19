<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;

class Trials extends Page implements HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;


    protected static string $view = 'filament.station.pages.trials';

    protected static ?string $navigationLabel = 'All Trials';

    protected static ?string $title = 'Inmates On Trial';

    protected ?string $subheading = 'Manage and track inmates currently on trial';

    protected static ?string $model = RemandTrial::class;

    protected static ?string $navigationGroup = 'Trials';


    public function table(Table $table): Table
    {
        return $table
            ->query(RemandTrial::query()
                ->where('detention_type', 'trial')
                ->orderBy('created_at', 'DESC'))
            ->emptyStateHeading('No Inmate On Trial Found')
            ->emptyStateDescription('Station has no inmates on trial yet...')
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
            Action::make('Discharge')
                ->color('green')
                ->button()
                ->icon('heroicon-m-arrow-right-start-on-rectangle')
                ->modalHeading('Trial Discharge')
                ->modalSubmitActionLabel('Discharge Imate')
                ->action(function (array $data, $record) {
                    app(\App\Services\DischargeService::class)
                        ->dischargeInmate($record, $data);
                    Notification::make()
                        ->success()
                        ->title('Inmate Discharged')
                    ->body("{$record->full_name} has been discharged successfully.")
                        ->send();
            })
                ->label('Discharge')
                ->fillForm(fn(RemandTrial $record): array => [
                    'serial_number' => $record->serial_number,
                'full_name' => $record->full_name,
                    'age_on_admission' => $record->age_on_admission,
                'detention_type' => $record->detention_type,
                'country_of_origin' => $record->country_of_origin,
                'offense' => $record->offense,
                'court' => $record->court,
                'next_court_date' => $record->next_court_date,
                'police_station' => $record->police_station,
                'police_officer' => $record->police_officer,
                'police_contact' => $record->police_contact,
                //'date_of_discharge' => $record->date_of_discharge,
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
                    TextInput::make('full_name')
                                        ->required()
                                        ->placeholder('e.g. Nana Kwame')
                        ->label('Inmate Name'),
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
                    //Edit remand action
                    EditAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Trial Updated')
                                ->body('The inmates trial has been updated successfully.'),
            )
                        ->modalHeading('Edit Trial Details')
                        ->label('Edit')
                ->button()
                ->color('blue')
                ->form([
                            Section::make('Inmate Details')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('serial_number')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->placeholder('e.g. NSW/06/25')
                                        ->label('Serial Number'),
                    TextInput::make('full_name')
                                        ->required()
                                        ->placeholder('e.g. Nana Kwame')
                                        ->label('Inmate Name'),
                                    TextInput::make('age_on_admission')
                                        ->numeric()
                                        ->minValue(15)
                                        ->placeholder('e.g. 30')
                                        ->required()
                                        ->label('Age on Admission'),
                    TextInput::make('country_of_origin')
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
                ]),
            ]);
    }
}
