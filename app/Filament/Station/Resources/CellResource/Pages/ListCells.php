<?php

namespace App\Filament\Station\Resources\CellResource\Pages;

use App\Filament\Station\Resources\CellResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCells extends ListRecords
{
    protected static string $resource = CellResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
