<?php

namespace App\Filament\HQ\Resources\StationResource\Pages;

use App\Filament\HQ\Resources\StationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStation extends EditRecord
{
    protected static string $resource = StationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
