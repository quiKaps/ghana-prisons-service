<?php

namespace App\Filament\Station\Resources\ConvictDischargeResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Station\Resources\ConvictDischargeResource;

class EditConvictDischarge extends EditRecord
{
    protected static string $resource = ConvictDischargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}
