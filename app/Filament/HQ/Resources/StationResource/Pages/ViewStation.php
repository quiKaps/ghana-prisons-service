<?php

namespace App\Filament\HQ\Resources\StationResource\Pages;

use App\Filament\HQ\Resources\StationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStation extends ViewRecord
{
    protected static string $resource = StationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
