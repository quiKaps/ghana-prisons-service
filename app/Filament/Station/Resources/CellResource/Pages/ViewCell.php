<?php

namespace App\Filament\Station\Resources\CellResource\Pages;

use App\Filament\Station\Resources\CellResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCell extends ViewRecord
{
    protected static string $resource = CellResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
