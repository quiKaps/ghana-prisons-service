<?php

namespace App\Filament\Station\Resources\ConvictDischargeResource\Pages;

use App\Filament\Station\Resources\ConvictDischargeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewConvictDischarge extends ViewRecord
{
    protected static string $resource = ConvictDischargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
