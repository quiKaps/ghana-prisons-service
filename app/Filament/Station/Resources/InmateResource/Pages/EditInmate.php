<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Station\Resources\InmateResource;

class EditInmate extends EditRecord
{
    protected static string $resource = InmateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            ViewAction::make()
                ->label('Profile')
                ->color('primary')
                ->icon('heroicon-o-user'),
            Action::make('Transfer')
                ->color('info')
                ->icon('heroicon-o-arrow-right-on-rectangle'),
            Action::make('Additional Sentence')
                ->color('green')
                ->icon('heroicon-o-plus-circle'),
            Action::make('Amnesty')
                ->color('warning')
                ->icon('heroicon-o-sparkles'),
            Action::make('Sentence Reduction')
                ->label('Sentence Reduction')
                ->color('purple')
                ->icon('heroicon-o-arrow-trending-down'),
        ];
    }

    public function getHeading(): string
    {
        return "Edit Convict";
    }
}
