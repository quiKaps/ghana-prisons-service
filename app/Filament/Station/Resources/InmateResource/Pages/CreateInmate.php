<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use App\Models\Sentence;
use App\Models\Discharge;
use App\Helpers\FormHelper;
use App\Models\RemandTrial;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Station\Resources\InmateResource;

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

        // $data['languages_spoken'] = json_encode($data['languages_spoken'] ?? []);
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
            'total_sentence' => $data['sentence'],
            'offence' => $data['offence'],
            'EPD' => $data['EPD'],
            'LPD' => $data['LPD'],
            'court_of_committal' => $data['court_of_committal'],
            'date_of_sentence' => $data['date_sentenced'],
            'warrant_document' => $data['warrant_document'],
        ]);

        //set user as dischagrged if epd is today
        if (isset($data['EPD']) && Carbon::parse($data['EPD']) == today()) {

            $this->record->update([
                'is_discharged' => true
            ]);

            Discharge::create([
                'station_id' =>  $this->record->station_id,
                'inmate_id' =>  $this->record->id,
                'discharge_type' => 'one-third remission',
                'discharge_date' => today(),
                //'reason' => $data['reason'],
            ]);}

            

            //Transfer-In Information
        if(isset($data['transferred_in'])){
          try {
            DB::transaction(function () use ($data) {
                            \App\Models\Transfer::create([
                                'inmate_id' => $this->record->id,
                                'from_station_id' => $data['station_transferred_from_id'],
                                'to_station_id' => Auth::user()->station_id,
                                'transfer_date' => $data['date_transferred_in'],
                                'status' => 'completed',
                                'requested_by' => Auth::id(),
                                'approved_by' => null,
                                'rejected_by' => null,
                            ]);
                        });
          } catch (\Throwable $e) {
            Log::error('Transfer Error'. $e .'');
          }
    }}

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
