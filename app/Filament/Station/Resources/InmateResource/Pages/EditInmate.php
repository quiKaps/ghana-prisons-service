<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        if (!$user->station) {
            Notification::make()
                ->title('Error')
                ->body('You do not have an assigned station. You cannot create an inmate.')
                ->danger()
                ->send();
            $this->halt();
        }

        $data['station_id'] = $user->station_id; // Current user station id

        //add gender from station type
        $data['gender'] = $user->station?->type === 'female' ? 'female' : 'male';

        $data['languages_spoken'] = json_encode($data['languages_spoken'] ?? []);
        // Ensure disability is boolean
        $data['disability'] = (bool) $data['disability'];

        $data['goaler'] = (bool) $data['goaler'];

        return $data;
    }
}
