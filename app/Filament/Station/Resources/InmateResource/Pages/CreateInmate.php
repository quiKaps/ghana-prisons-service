<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use App\Models\RemandTrial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\InmateResource;
use App\Helpers\FormHelper;
use App\Models\Sentence;

class CreateInmate extends CreateRecord
{
    protected static string $resource = InmateResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
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
        $data['gender'] = $user->station?->category === 'female' ? 'female' : 'male';

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


    public function getHeading(): string
    {
        return "Admit a Convict";
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label("Save"),
            ...(static::canCreateAnother() ? [$this->getCreateAnotherFormAction()->label("Save and Admit Another")] : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function afterCreate(): void
    {
        // Get the current form data
        $remand_id = Session::pull('used_remand_id');

        if ($remand_id) {
            RemandTrial::find($remand_id)?->delete();

            Notification::make()
                ->title('Inmate Re-admitted')
                ->body("{$this->record->full_name} has been successfully re-admitted.")
                ->success()
                ->send();
        }

        //get the form array data
        $data = $this->form->getState();

        //create sentence for inmate after inmate is created
        Sentence::create([
            'inmate_id' => $this->record->id,
            'sentence' => $data['sentence'],
            'offence' => $data['offence'],
            'EPD' => $data['EPD'],
            'LPD' => $data['LPD'],
            'court_of_committal' => $data['court_of_committal'],
            'date_of_sentence' => $data['date_sentenced'],
            'warrant_document' => $data['warrant_document'],
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Convict registered')
            ->body("{$this->record->full_name} has been admited successfully.");
    }
}
