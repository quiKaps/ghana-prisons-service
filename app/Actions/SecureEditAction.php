<?php

namespace App\Actions;

use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class SecureEditAction extends Action
{
    public static function make(?string $name = null, ?string $routeName = null): static
    {
        $action = parent::make($name ?? 'secureEdit');

        return $action
            ->label('Edit')
            ->icon('heroicon-o-pencil-square')
            ->color('primary')
            ->form([
                TextInput::make('password')
                    ->label('Confirm Password')
                    ->placeholder('Enter your password')
                    ->password()
                    ->required(),
            ])
            ->action(function (array $data, $record) use ($routeName) {
                if (! Hash::check($data['password'], auth()->user()->password)) {
                    Notification::make()
                        ->title('Incorrect Password')
                        ->body('You must confirm your password to edit this record.')
                        ->danger()
                        ->send();
                    return;
                }

                return redirect()->route(
                    $routeName ?? 'filament.admin.resources.remand-trials.edit',
                    ['record' => $record]
                );
            });
    }
}
