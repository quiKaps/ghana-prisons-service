<?php

namespace App\Filament\Station\Pages;


use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Station\Resources\InmateResource;
use App\Filament\Station\Resources\RemandTrialResource;
use App\Models\RemandTrial;
use Filament\Forms\Components\Actions\Action as FormAction;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $title = null;

    public $defaultAction = 'passwordResetPopUpModal';

    public function passwordResetPopUpModal(): Action
    {
        return Action::make('passwordResetPopUpModal')
            ->modalHeading('Create New Password')
            ->modalDescription('You must set a new password before using your account for the first time.')
            ->modalSubmitActionLabel('Save Password')
            ->modalIcon('heroicon-o-key')
            ->modalAlignment(Alignment::Center)
            ->visible(fn(): bool => Auth::user()->password_changed_at == null)
            ->modalWidth(MaxWidth::Large)
            ->form([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->placeholder('Enter your current password')
                    ->required()
                    ->live()
                    ->reactive()
                    ->password(fn(Get $get) => !$get('show_password'))
                    ->rule('current_password')
                    ->validationMessages([
                        'current_password' => 'The password you entered is incorrect.',
                    ])
                    ->suffixAction(
                        FormAction::make('togglePassword')
                            ->icon(fn(Get $get) => $get('show_password') ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                            ->label('')
                            ->action(fn(Get $get, Set $set) => $set('show_password', !$get('show_password')))
                            ->tooltip(fn(Get $get) => $get('show_password') ? 'Hide password' : 'Show password')
                    ),
                TextInput::make('new_password')
                    ->label('New Password')
                    ->placeholder('Enter new password')
                    ->helperText('Must be at least 8 characters and include a letter, number, and symbol.')
                    ->password(fn(Get $get) => !$get('show_password'))
                    ->required()
                    ->live()
                    ->reactive()
                    ->rule([
                        'required',
                        'min:8',
                        'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/',
                    ])->validationMessages([
                        'regex' => 'Password must include a letter, a number, and a symbol.',
                    ])
                    ->suffixAction(
                        FormAction::make('togglePassword')
                            ->icon(fn(Get $get) => $get('show_password') ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                            ->label('')
                            ->action(fn(Get $get, Set $set) => $set('show_password', !$get('show_password')))
                            ->tooltip(fn(Get $get) => $get('show_password') ? 'Hide password' : 'Show password')
                    ),
                TextInput::make('confirm_password')
                    ->label('Confirm Password')
                    ->placeholder('Confirm your new password')
                    ->password(fn(Get $get) => !$get('show_password'))
                    ->required()
                    ->live()
                    ->reactive()
                    ->same('new_password')
                    ->suffixAction(
                        FormAction::make('togglePassword')
                            ->icon(fn(Get $get) => $get('show_password') ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                            ->label('')
                            ->action(fn(Get $get, Set $set) => $set('show_password', !$get('show_password')))
                            ->tooltip(fn(Get $get) => $get('show_password') ? 'Hide password' : 'Show password')
                    )
                    ->validationMessages([
                        'same' => 'The password confirmation does not match new password.',
                    ])
            ])
            ->action(function (array $data) {
                //check if current password is the same as the authenticated user's password
                if (! Hash::check($data['current_password'], Auth::user()->password)) {
                    Notification::make()
                        ->title('Incorrect Password')
                        ->danger()
                        ->body('You must confirm your current password to complete this action.')
                        ->send();

                    return;
                }

                //compare the new password and the confirm password
                if ($data['new_password'] !== $data['confirm_password']) {
                    Notification::make()
                        ->title('Password Mismatch')
                        ->danger()
                        ->body('You new password and confirm password must be the same.')
                        ->send();

                    return;
                }

                try {

                    $user = Auth::user();

                    $user->update([
                        'password' => Hash::make($data['new_password']),
                        'password_changed_at' => now()
                    ]);

                    return redirect('/login');

                    Notification::make()
                        ->title('Password Reset Success')
                        ->success()
                        ->body('Your password has been updated successfully.')
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Error')
                        ->danger()
                        ->body('Unknown error, please try again' . $e)
                        ->send();
                }
            })
        ;
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_inmate')
                ->label('Admit a Convict')
                ->url(fn() => InmateResource::getUrl('create'))
                ->visible(fn() => Auth::user()?->user_type === 'officer')
                ->icon('heroicon-o-plus'),
            Action::make('add_remand_trial')
                ->label('Admit on Remand/Trial')
                ->visible(fn() => Auth::user()?->user_type === 'officer')
                ->color('blue')
                ->url(fn() => RemandTrialResource::getUrl('create'))
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTitle(): string
    {
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 18) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }
        $name = Auth::check() ? Auth::user()->name : 'Guest';
        return "{$greeting}, {$name}";
    }
}

