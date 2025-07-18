<?php

namespace App\Filament\Station\Resources\CellResource\Pages;

use App\Filament\Station\Resources\CellResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCell extends EditRecord
{
    protected static string $resource = CellResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
