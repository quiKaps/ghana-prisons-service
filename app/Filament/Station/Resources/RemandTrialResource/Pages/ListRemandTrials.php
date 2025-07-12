<?php

namespace App\Filament\Station\Resources\RemandTrialResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Station\Resources\RemandTrialResource;

class ListRemandTrials extends ListRecords
{
    protected static string $resource = RemandTrialResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(
            Auth::user()->user_type === 'prison_admin',
            403,
            'Unauthorized Action!'
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
