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
            Action::make('back')
                ->label('Back to Convicts')
                ->color('success')
                ->icon('heroicon-o-arrow-left')
                ->url(InmateResource::getUrl('index')),

            Action::make('view')
                ->label('View Profile')
                ->icon('heroicon-o-user')
                ->url(fn() => InmateResource::getUrl('view', ['record' => $this->getRecord()]))
                ->color('blue'),

            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->label('Confirm Password')
                        ->placeholder('Enter your password')
                        ->password()
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    if (! \Illuminate\Support\Facades\Hash::check($data['password'], auth()->user()->password)) {
                        \Filament\Notifications\Notification::make()
                            ->title('Incorrect Password')
                            ->danger()
                            ->body('You must confirm your password to delete this record.')
                            ->send();
                        return;
                    }
                    $record->delete();

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Record Deleted')
                        ->send();
                }),
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
