<?php

namespace App\Filament\Station\Resources\TrialResource\Pages;

use App\Filament\Station\Resources\TrialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrial extends EditRecord
{
    protected static string $resource = TrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
