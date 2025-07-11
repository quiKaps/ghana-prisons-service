<?php

namespace App\Filament\HQ\Resources\TrialResource\Pages;

use App\Filament\HQ\Resources\TrialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrials extends ListRecords
{
    protected static string $resource = TrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
