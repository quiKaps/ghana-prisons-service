<?php

namespace App\Filament\Station\Resources\TransfersResource\Pages;

use App\Filament\Station\Resources\TransfersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransfers extends EditRecord
{
    protected static string $resource = TransfersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
