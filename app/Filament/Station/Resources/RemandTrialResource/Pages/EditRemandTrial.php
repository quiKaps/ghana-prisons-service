<?php

namespace App\Filament\Station\Resources\RemandTrialResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Station\Resources\RemandTrialResource;

class EditRemandTrial extends EditRecord
{
    protected static string $resource = RemandTrialResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(
            Auth::user()->user_type === 'prison_admin',
            403,
            'Unauthorized Action!'
        );
    }

    protected function getHeaderActions(): array
    {
        return [

            // Show "Back to Remands" if detention_type is 'remand'
            Actions\Action::make('back-to-remands')
                ->label('Back to Remands')
                ->icon('heroicon-o-arrow-left')
                ->color('success')
                ->visible(fn($record) => $record->detention_type === 'remand')
                ->url(fn() => route('filament.station.resources.remands.index')),

            // Show "Back to Trials" if detention_type is 'trial'
            Actions\Action::make('back-to-trials')
                ->label('Back to Trials')
                ->icon('heroicon-o-arrow-left')
                ->color('success')
                ->visible(fn($record) => $record->detention_type === 'trial')
                ->url(fn() => route('filament.station.resources.trials.index')),
            //back to trials or remand action ends


            Actions\ViewAction::make()
                ->label('Profile')
                ->icon('heroicon-o-user')
                ->color('blue'),
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->label('Confirm Password')
                        ->placeholder('Enter your password')
                        ->password()
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                if (! \Illuminate\Support\Facades\Hash::check($data['password'], Auth::user()->password)) {
                        \Filament\Notifications\Notification::make()
                            ->title('Incorrect Password')
                            ->danger()
                            ->body('You must confirm your password to delete this record.')
                        ->send();
                        return;
                }
                    $record->delete();

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Record Deleted')
                        ->send();
                }),
        ];
    }

    public function getHeading(): string
    {
        return "Edit {$this->record->full_name}'s Profile";
    }
}
