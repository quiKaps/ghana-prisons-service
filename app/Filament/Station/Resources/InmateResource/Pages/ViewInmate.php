<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use App\Filament\Station\Resources\InmateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInmate extends ViewRecord
{
    protected static string $resource = InmateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }
}
