<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use Filament\Actions\ViewAction;
use App\Actions\SecureEditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use App\Actions\SecureDeleteAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Station\Resources\InmateResource;

class ViewInmate extends ViewRecord
{
    protected static string $resource = InmateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Convicts')
                ->color('success')
                ->icon('heroicon-o-arrow-left')
                ->url(InmateResource::getUrl('index')),
            Action::make('print')
                ->label('Print Profile')
                ->color('info')
                ->icon('heroicon-o-printer'),
            ActionGroup::make([
                ViewAction::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user'),
                Action::make('Transfer')->icon('heroicon-o-arrow-right-on-rectangle'),
                Action::make('Special Discharge')->icon('heroicon-o-arrow-right-on-rectangle'),
                Action::make('Additional Sentence')->icon('heroicon-o-plus-circle'),
                Action::make('Amnesty')->icon('heroicon-o-sparkles'),
                Action::make('Sentence Reduction')
                    ->icon('heroicon-o-arrow-trending-down'),
                SecureEditAction::make('edit', 'filament.admin.resources.inmates.edit')
                    ->modalWidth('md')
                    ->modalHeading('Protected Data Access')
                    ->modalDescription('This is a secure area of the application. Please confirm your password before continuing.')
                    ->label('Edit'),
                SecureDeleteAction::make('delete')
                    ->label('Delete'),
            ])
                ->button()
                ->label('More Actions'),

        ];
    }

    public function getHeading(): string
    {
        return "{$this->record->full_name}'s Profile";
    }
}
