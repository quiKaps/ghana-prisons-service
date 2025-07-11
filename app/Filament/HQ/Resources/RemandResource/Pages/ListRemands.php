<?php

namespace App\Filament\HQ\Resources\RemandResource\Pages;

use App\Filament\HQ\Resources\RemandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRemands extends ListRecords
{
    protected static string $resource = RemandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
