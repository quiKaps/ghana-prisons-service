<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\InmateResource;
use Filament\Notifications\Notification;

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
        // $lastInmate = \App\Models\Inmate::orderBy('id', 'desc')->first();
        // $columnNumber = $lastInmate ? $lastInmate->id + 1 : 1;
        // $year = date('y');
        // $stationCode = $user->station->code;
        // $data['serial_number'] = "{$stationCode}/{$columnNumber}/{$year}";

        $data['medical_conditions'] = json_encode($data['medical_conditions'] ?? []);
        $data['allergies'] = json_encode($data['allergies'] ?? []);
        $data['languages_spoken'] = json_encode($data['languages_spoken'] ?? []);
        $data['disability'] = (bool) $data['disability'];

        $data['middle_name'] = empty($data['middle_name']) ? null : $data['middle_name'];
        $data['goaler_document'] = empty($data['goaler_document']) ? null : $data['goaler_document'];
        $data['warrant_document'] = empty($data['warrant_document']) ? null : $data['warrant_document'];
        //$data['goaler'] = (bool) $data['goaler'];

        //dd($data); // Debugging line to inspect the data before creation

        return $data;
    }
}
