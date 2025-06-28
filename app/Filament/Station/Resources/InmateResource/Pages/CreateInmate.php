<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use App\Models\RemandTrial;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\InmateResource;

class CreateInmate extends CreateRecord
{
    protected static string $resource = InmateResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if (!$user->station) {
            Notification::make()
                ->title('Error')
                ->body('You do not have an assigned station. You cannot create an inmate.')
                ->danger()
                ->send();
            $this->halt();
        }

        $data['station_id'] = $user->station_id; // Current user station id

        $data['medical_conditions'] = json_encode($data['medical_conditions'] ?? []);
        $data['allergies'] = json_encode($data['allergies'] ?? []);
        $data['languages_spoken'] = json_encode($data['languages_spoken'] ?? []);
        $data['disability'] = (bool) $data['disability'];

        $data['goaler_document'] = empty($data['goaler_document']) ? null : $data['goaler_document'];
        $data['warrant_document'] = empty($data['warrant_document']) ? null : $data['warrant_document'];
        //$data['goaler'] = (bool) $data['goaler'];

        // Debugging line to inspect the data before creation

        return $data;
    }

    public function getHeading(): string
    {
        return "Admit a Convict";
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label("Save"),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()->label("Save and Admit Another")] : []),
            $this->getCancelFormAction(),
        ];
    }

    // protected function afterCreate(): void
    // {
    //     if (request()->has('from_remand')) {
    //         dd('here');
    //         RemandTrial::find(request('from_remand'))?->delete();

    //         Notification::make()
    //             ->title('Inmate Re-admitted')
    //             ->body("{$this->record->full_name} has been successfully re-admitted.")
    //             ->success()
    //             ->send();
    //     }
    // }
}
