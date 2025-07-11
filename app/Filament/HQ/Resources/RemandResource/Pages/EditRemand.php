<?php

namespace App\Filament\HQ\Resources\RemandResource\Pages;

use App\Filament\HQ\Resources\RemandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemand extends EditRecord
{
    protected static string $resource = RemandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
