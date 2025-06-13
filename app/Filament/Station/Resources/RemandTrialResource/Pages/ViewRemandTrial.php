<?php

namespace App\Filament\Station\Resources\RemandTrialResource\Pages;

use App\Filament\Station\Resources\RemandTrialResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRemandTrial extends ViewRecord
{
    protected static string $resource = RemandTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
