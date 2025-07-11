<?php

namespace App\Filament\HQ\Resources\ConvictResource\Pages;

use App\Filament\HQ\Resources\ConvictResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConvicts extends ListRecords
{
    protected static string $resource = ConvictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
