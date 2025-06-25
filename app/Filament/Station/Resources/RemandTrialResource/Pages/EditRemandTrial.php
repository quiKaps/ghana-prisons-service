<?php

namespace App\Filament\Station\Resources\RemandTrialResource\Pages;

use App\Filament\Station\Resources\RemandTrialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemandTrial extends EditRecord
{
    protected static string $resource = RemandTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
                    if (! \Illuminate\Support\Facades\Hash::check($data['password'], auth()->user()->password)) {
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
