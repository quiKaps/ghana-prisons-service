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
            // Show "Back to Remands" if detention_type is 'remand'
            Actions\Action::make('back-to-remands')
                ->label('Back to Remands')
                ->icon('heroicon-o-arrow-left')
                ->color('success')
                ->visible(fn($record) => $record->detention_type === 'remand')
                ->url(fn() => route('filament.station.pages.remand')),

            // Show "Back to Trials" if detention_type is 'trial'
            Actions\Action::make('back-to-trials')
                ->label('Back to Trials')
                ->icon('heroicon-o-arrow-left')
                ->color('success')
                ->visible(fn($record) => $record->detention_type === 'trial')
                ->url(fn() => route('filament.station.pages.trials')),
            //back to trials or remand action ends

            //print action starts
            Actions\Action::make('Print')
                ->color('warning')
                ->icon('heroicon-s-printer'),
            //print action ends

            //readmission action starts
            Action::make('Re-admission')
                ->icon('heroicon-s-arrow-path')
                ->color('info')
                ->button()
                ->action(function ($record) {
                    session(['remand_id' => $record->id]);
                    return redirect()->route('filament.station.resources.inmates.create');
                })
                ->visible(fn($record) => $record->detention_type === 'trial')
                ->requiresConfirmation()
                ->modalHeading('Re-admit this inmate?')
                ->modalSubmitActionLabel('Proceed to Admission'),
            //readmission action ends


            //discharge action starts
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

            //discharge action ends

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
        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }
}
