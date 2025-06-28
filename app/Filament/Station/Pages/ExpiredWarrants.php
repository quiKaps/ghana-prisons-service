<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\RemandTrial;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;

class ExpiredWarrants extends Page implements \Filament\Tables\Contracts\HasTable
{

    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.station.pages.expired-warrants';

    protected static ?string $navigationGroup = 'Remand and Trials';

    protected static ?string $navigationLabel = 'Expired Warrants';

    protected static ?string $title = 'Expired Warrants';

    protected ?string $subheading = 'List of inmates with expired remand warrants';

    public function table(Table $table): Table
    {
        return $table
            ->query(RemandTrial::query()
                ->where('detention_type', 'remand')
                ->where('next_court_date', '<', now()))
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
                ->color('danger')
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
}
