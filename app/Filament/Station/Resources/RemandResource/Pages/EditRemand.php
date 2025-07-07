<?php

namespace App\Filament\Station\Resources\RemandResource\Pages;

use App\Filament\Station\Resources\RemandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemand extends EditRecord
{
    protected static string $resource = RemandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
