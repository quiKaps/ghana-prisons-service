<?php

namespace App\Filament\Station\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if (!$user->station) {
            Notification::make()
                ->title('Error')
                ->body('You do not have an assigned station. You cannot create a user.')
                ->danger()
                ->send();

            $this->halt();
        }

        $data['station_id'] = $user->station_id; // Current user station id
        $data['password'] = Hash::make('password'); // Hash the password before saving

        return $data;
    }
}
