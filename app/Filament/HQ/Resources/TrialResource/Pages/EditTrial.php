<?php

namespace App\Filament\HQ\Resources\TrialResource\Pages;

use App\Filament\HQ\Resources\TrialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrial extends EditRecord
{
    protected static string $resource = TrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
