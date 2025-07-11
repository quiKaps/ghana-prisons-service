<?php

namespace App\Filament\HQ\Resources\ConvictResource\Pages;

use App\Filament\HQ\Resources\ConvictResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConvict extends EditRecord
{
    protected static string $resource = ConvictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
