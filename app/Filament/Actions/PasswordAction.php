<?php

namespace App\Filament\Actions;

use Filament\Forms;
use Filament\Actions\Action;

class PasswordAction extends Action
{
    protected function isPasswordSessionValid(): bool
    {
        return (session()->has('auth.password_confirmed_at') && (time() - session('auth.password_confirmed_at', 0)) < 300);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->isPasswordSessionValid()) {
            $this->requiresConfirmation()
                ->modalHeading('Confirm password')
                ->modalDescription('Please confirm your password to complete this action.')
                ->form([
                    Forms\Components\TextInput::make('current_password')
                        ->required()
                        ->password()
                        ->rule('current_password'),
                ]);
        }
    }

    public function handle(array $data): void
    {
        if (! $this->isPasswordSessionValid()) {
            session(['auth.password_confirmed_at' => time()]);
        }

        parent::handle($data);
    }
}
