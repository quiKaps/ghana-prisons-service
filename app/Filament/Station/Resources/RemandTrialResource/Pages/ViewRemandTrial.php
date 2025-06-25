<?php

namespace App\Filament\Station\Resources\RemandTrialResource\Pages;

use Filament\Actions;
use App\Models\RemandTrial;
use Filament\Actions\Action;
use App\Actions\SecureEditAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\DatePicker;
use App\Filament\Station\Resources\RemandTrialResource;

class ViewRemandTrial extends ViewRecord
{
    protected static string $resource = RemandTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Print')
                ->color('info')
                ->icon('heroicon-s-printer'),
            Actions\Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->modalWidth('md')
                ->modalHeading('Protected Data Access')
                ->modalDescription('This is a secure area of the application. Please confirm your password within the modal before continuing.')
                ->form([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->label('Confirm Password')
                        ->placeholder('Enter your password')
                        ->password()
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    if (! \Illuminate\Support\Facades\Hash::check($data['password'], auth()->user()->password)) {
                        \Filament\Notifications\Notification::make()
                            ->title('Incorrect Password')
                            ->body('You must confirm your password to edit this record.')
                            ->danger()
                            ->send();
                        return;
                }
                    return redirect()->route(
                        'filament.station.resources.remand-trials.edit',
                        ['record' => $record]
                    );
                }),
            Action::make('Discharge')
                ->color('green')
                ->button()
                ->icon('heroicon-m-arrow-right-start-on-rectangle')
                ->modalHeading('Trial Discharge')
                ->modalSubmitActionLabel("Discharge Prisoner")
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
                    Group::make()
                        ->columns(2)
                        ->schema([
                            TextInput::make('serial_number')
                                ->readOnly()
                                ->label('Serial Number'),
                            TextInput::make('full_name')
                                ->readOnly()
                        ->label("Prisoner's Name"),
                        ])->columns(2),
                    Group::make()
                        ->columns(2)
                        ->schema([
                            TextInput::make('offense')
                                ->label('Offense')
                                ->readOnly(),
                            TextInput::make('court')
                                ->label('Court')
                                ->readOnly(),
                            TextInput::make('next_court_date')
                                ->label('Next Court Date')
                                ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d') : '')
                                ->readOnly(),
                            TextInput::make('police_station')
                                ->label('Police Station')
                                ->readOnly(),
                            TextInput::make('police_officer')
                                ->label('Police Officer')
                                ->readOnly(),
                            TextInput::make('police_contact')
                                ->label('Police Contact')
                                ->readOnly(),
                        ]),
                    Section::make('Discharge Details')
                        ->columns(2)
                        ->schema([
                            DatePicker::make('date_of_discharge')
                                ->required()
                                ->default(now()->toDateString())
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
                ])
        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }
}
