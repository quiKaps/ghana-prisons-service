<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;

class Remand extends Page implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.station.pages.remand';

    protected static ?string $navigationGroup = 'Remand and Trials';

    protected static ?string $navigationLabel = 'All Remands';

    protected static ?string $title = 'Prisoners On Remand';

    protected ?string $subheading = "View and manage remand prisoners";

    protected static ?string $model = RemandTrial::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(RemandTrial::query()
                ->where('detention_type', 'remand')
            ->where('next_court_date', '>=', now())
            ->orderBy('created_at', 'DESC'))
            ->columns([
                TextColumn::make('serial_number')
                ->weight(FontWeight::Bold)
                ->label('S.N.'),
            TextColumn::make('full_name')
                    ->searchable()
                ->label("Name of Prisoner"),
            TextColumn::make('offense')
                ->label('Offence'),
            TextColumn::make('admission_date')
                ->date()
                ->label('Date of Admission'),
                TextColumn::make('next_court_date')
                ->badge()
                ->color('success')
                    ->label('Next Court Date')
                ->date(),
            TextColumn::make('court')
                ->label('Court of Committal'),

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
                ->modalSubmitActionLabel('Discharge Prisoner')
                ->action(function (array $data, $record) {
                    app(\App\Services\DischargeService::class)
                        ->dischargeInmate($record, $data);
                    Notification::make()
                        ->success()
                    ->title('Prisoner Discharged')
                    ->body("{$record->full_name} has been discharged successfully.")
                        ->send();
            })
                ->label('Discharge')
                ->fillForm(fn(RemandTrial $record): array => [
                    'serial_number' => $record->serial_number,
                'full_name' => $record->full_name,
                'admission_date' => date_format($record->admission_date, 'Y-m-d'),
                'age_on_admission' => $record->age_on_admission,
                'detention_type' => $record->detention_type,
                'country_of_origin' => $record->country_of_origin,
                'offense' => $record->offense,
                'court' => $record->court,
                'next_court_date' => date_format($record->next_court_date, 'Y-m-d'),
                'police_station' => $record->police_station,
                'police_officer' => $record->police_officer,
                'police_contact' => $record->police_contact,
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
                            'other' => 'Other',
                        ])
                        ->label('Mode of Discharge'),
                    ])->columns(2),
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
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return RemandTrial::query()
            ->where('detention_type', 'remand')
            ->whereDate('next_court_date', now()->addDay()->toDateString())
            ->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of remand inmates who have a court appearance tomorrow';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
