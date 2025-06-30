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
        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }
}
