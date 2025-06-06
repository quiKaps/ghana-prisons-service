<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use Illuminate\Container\Attributes\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\InmateResource;

class CreateInmate extends CreateRecord
{
    protected static string $resource = InmateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array

    {
        $data['station_id'] = auth()->user()->station_id; // Current user station id
        $lastInmate = \App\Models\Inmate::orderBy('id', 'desc')->first();
        $columnNumber = $lastInmate ? $lastInmate->id + 1 : 1;
        $year = date('y');
        $stationCode = auth()->user()->station->code ?? 'UNKNOWN';
        $data['serial_number'] = "{$stationCode}/{$columnNumber}/{$year}";
        return $data;
    }
}
