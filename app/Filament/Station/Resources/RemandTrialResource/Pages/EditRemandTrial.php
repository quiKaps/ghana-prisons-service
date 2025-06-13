<?php

namespace App\Filament\Station\Resources\RemandTrialResource\Pages;

use App\Filament\Station\Resources\RemandTrialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemandTrial extends EditRecord
{
    protected static string $resource = RemandTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
