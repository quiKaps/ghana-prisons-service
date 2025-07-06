<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use App\Models\Sentence;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
                if (! \Illuminate\Support\Facades\Hash::check($data['password'], Auth::user()->password)) {
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

    //add the data to the form

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $latestSentence = Sentence::where('inmate_id', $data['id'])
            ->latest()
            ->first();

        $data['offence'] = $latestSentence->offence;
        $data['sentence'] = $latestSentence->sentence;
        $data['date_sentenced'] = $latestSentence->date_of_sentence;
        $data['EPD'] = $latestSentence->EPD;
        $data['LPD'] = $latestSentence->LPD;
        $data['court_of_committal'] = $latestSentence->court_of_committal;
        $data['warrant_document'] = $latestSentence->warrant_document;

        Session::put('latestSentenceId', $latestSentence->id); // temporarily store it again for afterCreate


        return $data;
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

        //Access the current the sentence id
        $sentence = Sentence::find(Session::pull('latestSentenceId'));

        $sentence->update([
            'offence' => $data['offence'],
            'sentence' => $data['sentence'],
            'date_of_sentence' => $data['date_sentenced'],
            'EPD' => $data['EPD'],
            'LPD' => $data['LPD'],
            'court_of_committal' => $data['court_of_committal'],
            'warrant_document' => $data['warrant_document'],
        ]);

        $data['station_id'] = $user->station_id; // Current user station id

        //add gender from station type
        $data['gender'] = $user->station?->type === 'female' ? 'female' : 'male';

        $data['languages_spoken'] = json_encode($data['languages_spoken'] ?? []);
        // Ensure disability is boolean
        $data['disability'] = (bool) $data['disability'];

        $data['goaler'] = (bool) $data['goaler'];

        if ($data['religion'] === 'other_religion' && !empty($data['religion_other'])) {
            $data['religion'] = $data['religion_other'];
        }

        unset($data['religion_other']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Prisoner information updated successfully';
    }

    //Redirect to the profile page after save
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
