<?php

namespace App\Filament\HQ\Resources\RemandResource\Pages;

use App\Filament\HQ\Resources\RemandResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRemand extends ViewRecord
{
    protected static string $resource = RemandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
