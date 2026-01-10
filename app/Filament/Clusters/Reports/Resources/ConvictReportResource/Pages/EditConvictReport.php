<?php

namespace App\Filament\Clusters\Reports\Resources\ConvictReportResource\Pages;

use App\Filament\Clusters\Reports\Resources\ConvictReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConvictReport extends EditRecord
{
    protected static string $resource = ConvictReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
