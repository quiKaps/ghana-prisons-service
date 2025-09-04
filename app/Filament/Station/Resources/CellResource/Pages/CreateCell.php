<?php

namespace App\Filament\Station\Resources\CellResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\CellResource;

class CreateCell extends CreateRecord
{
    protected static string $resource = CellResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array

    {
        // Automatically assign the station_id based on the logged-in user's station
        $data['station_id'] = Auth::user()->station_id;

        // Return the modified data array
        return $data;
    }
}
