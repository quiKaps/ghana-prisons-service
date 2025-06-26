<?php

namespace App\Filament\Station\Resources\RemandTrialResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\RemandTrialResource;

class CreateRemandTrial extends CreateRecord
{
    protected static string $resource = RemandTrialResource::class;

    public function getTitle(): string
    {
        return 'Remand & Trial Admission Form';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if (!$user->station) {
            Notification::make()
                ->title('Error')
                ->body('You do not have an assigned station. You cannot create a remand or trial.')
                ->danger()
                ->send();

            $this->halt();
        }

        $data['station_id'] = $user->station_id; // Current user station id

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label("Save"),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()->label("Save and Admit Another")] : []),
            $this->getCancelFormAction(),
        ];
    }
}
