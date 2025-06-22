<?php

namespace App\Actions;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class SecureDeleteAction extends Action
{
    public static function make(?string $name = null): static
    {
        $action = parent::make($name ?? 'secureDelete');

        // continue chaining...
        return $action
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
            });
    }
}
