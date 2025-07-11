<?php

namespace App\Filament\HQ\Resources\ConvictResource\Pages;

use App\Filament\HQ\Resources\ConvictResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewConvict extends ViewRecord
{
    protected static string $resource = ConvictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
