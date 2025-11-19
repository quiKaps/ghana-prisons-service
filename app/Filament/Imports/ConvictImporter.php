<?php

namespace App\Filament\Imports;

use Log;
use App\Models\Inmate;
use App\Models\Sentence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Support\Facades\Log as logginfg;

class ConvictImporter extends Importer
{
    protected static ?string $model = Inmate::class;

    private array $sentenceData = [];

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('prisoner_picture')
                ->label('Prisoner Picture')
                ->guess(['PrisonerPicture', 'Picture', 'Photo'])
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('serial_number')
                ->requiredMapping()
                ->label('Serial Number')
                ->guess(['SerialNumber', 'SN'])
                ->example('SN-2025-0001')
                ->rules(['required', 'max:255']),

            ImportColumn::make('full_name')
                ->requiredMapping()
                ->label('Full Name')
                ->guess(['FullName', 'PrisonerName', 'Name'])
                ->example('John Doe')
                ->rules(['required', 'max:255']),

            ImportColumn::make('gender')
                ->label('Gender')
                ->guess(['Gender', 'Sex'])
                ->rules(['nullable', 'max:50']),

            ImportColumn::make('age_on_admission')
                ->label('Age on Admission')
                ->guess(['AgeOnAdmission'])
                ->castStateUsing(function ($state) {
        if (blank($state)) return null;

        return (int) filter_var($state, FILTER_SANITIZE_NUMBER_INT);
    })
    ->rules(['required', 'integer', 'min:10', 'max:120']),

            ImportColumn::make('sentence')
                ->label('Sentence')
                ->guess(['Sentence'])
                ->example('2 years')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('date_of_sentence')
                ->label('Date of Sentence')
                ->guess(['DateOfSentence'])
                ->castStateUsing(function (?string $state): ?string {
    if (blank($state)) return null;

    try {
        return \Carbon\Carbon::parse($state)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
})                ->rules(['nullable', 'date']),

            ImportColumn::make('epd')
                ->label('EPD')
                ->guess(['EPD'])
                ->castStateUsing(function (?string $state): ?string {
    if (blank($state)) return null;

    try {
        return \Carbon\Carbon::parse($state)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
})                ->rules(['nullable', 'date']),

            ImportColumn::make('lpd')
                ->label('LPD')
                ->guess(['LPD'])
                ->castStateUsing(function (?string $state): ?string {
    if (blank($state)) return null;

    try {
        return \Carbon\Carbon::parse($state)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
})                ->rules(['nullable', 'date']),

 ImportColumn::make('admission_date')
                ->label('Date of Admission')
                ->guess(['DateOfAdmission'])
                ->castStateUsing(function (?string $state): ?string {
    if (blank($state)) return null;

    try {
        return \Carbon\Carbon::parse($state)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
})                ->rules(['required', 'date']),

            ImportColumn::make('offence')
                ->label('Offence')
                ->guess(['Offence'])
                ->example('Theft')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('previously_convicted')
                ->label('Previously Convicted')
                ->guess(['PreviouslyConvicted', 'Previously_Convicted'])
                ->example('Yes / No')
                ->castStateUsing(function (?string $state): ?int {
    if (blank($state)) {
        return 0;
    }
    return in_array(strtolower(trim($state)), ['1','yes','y','true','t']) ? 1 : 0;
})

                ->rules(['nullable']),

            ImportColumn::make('previous_sentence')
                ->label('Previous Sentence')
                ->guess(['PreviousSentence'])
                ->example('2 years')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('previous_offence')
                ->label('Previous Offence')
                ->guess(['PreviousOffence'])
                ->example('Theft')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('previous_station_id')
                ->label('Previous Station (identifier)')
                ->guess(['PreviousStation', 'PreviousStationId'])
                ->example('STN-001')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('station_id')
                ->label('Current Station (id or name)')
                ->guess(['Station', 'StationId'])
                ->example('1 or Accra Central')
                ->rules(['nullable']),

            ImportColumn::make('cell_id')
                ->label('Block and Cell')
                ->guess(['CellID', 'Cell', 'BlockAndCell'])
                ->example('A-01')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('court_of_committal')
                ->label('Court of Committal')
                ->guess(['Court', 'CourtOfCommittal'])
                ->example('Accra District Court')
                ->rules(['nullable', 'max:255']),

            // Next of kin
            ImportColumn::make('next_of_kin_name')
                ->label('Next of Kin Name')
                ->guess(['NextOfKinName', 'NextOfKin'])
                ->example('Jane Doe')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('next_of_kin_relationship')
                ->label('Next of Kin Relationship')
                ->guess(['NextOfKinRelationship'])
                ->example('Sister')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('next_of_kin_contact')
                ->label('Next of Kin Contact')
                ->guess(['NextOfKinContact', 'NextOfKinPhone'])
                ->example('+233201234567')
                ->rules(['nullable', 'max:255']),

            // Personal details
            ImportColumn::make('religion')
                ->label('Religion')
                ->guess(['ReligiousBackground', 'Religion'])
                ->example('Christian')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('nationality')
                ->requiredMapping()
                ->label('Nationality')
                ->guess(['Nationality', 'CountryofOrigin', 'nationality'])
                ->example('Ghana')
                ->rules(['required', 'max:255']),

            ImportColumn::make('education_level')
                ->label('Education Level')
                ->guess(['EducationLevel', 'EducationalBackground'])
                ->example('Secondary')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('occupation')
                ->label('Occupation')
                ->guess(['Occupation'])
                ->example('Farmer')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('hometown')
                ->label('Hometown')
                ->guess(['Hometown'])
                ->example('Kumasi')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('tribe')
                ->label('Tribe')
                ->guess(['Tribe'])
                ->example('Akan')
                ->rules(['nullable', 'max:255']),

            // Physical characteristics
            ImportColumn::make('distinctive_marks')
                ->label('Distinctive Marks')
                ->guess(['DistinctiveMarks', 'Marks'])
                ->example('Scar on left cheek; Tattoo on arm')
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }
                    $parts = array_filter(array_map('trim', preg_split('/[;,]+/', $state)));
                    return empty($parts) ? null : json_encode(array_values($parts));
                })
                ->rules(['nullable']),

            ImportColumn::make('part_of_the_body')
                ->label('Part of the Body (distinctive)')
                ->guess(['PartOfTheBody'])
                ->example('Left cheek')
                ->rules(['nullable', 'max:255']),

            // Languages
            ImportColumn::make('languages_spoken')
                ->label('Languages Spoken')
                ->guess(['Languages', 'LanguagesSpoken'])
                ->example('English; Twi')
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }
                    $parts = array_filter(array_map('trim', preg_split('/[;,]+/', $state)));
                    return empty($parts) ? null : json_encode(array_values($parts));
                })
                ->rules(['nullable']),

            // Disability
            ImportColumn::make('disability')
                ->label('Disability')
                ->guess(['Disability'])
                ->example('Yes / No')
                ->castStateUsing(function (?string $state): ?int {
                    if (blank($state)) {
                        return 0;
                    }
                    $s = strtolower(trim($state));
                    return in_array($s, ['1', 'yes', 'y', 'true', 't']) ? 1 : 0;
                })
                ->rules(['nullable']),

            ImportColumn::make('disability_type')
                ->label('Disability Type')
                ->guess(['DisabilityType'])
                ->example('Visual impairment')
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }
                    $parts = array_filter(array_map('trim', preg_split('/[;,]+/', $state)));
                    return empty($parts) ? null : json_encode(array_values($parts));
                })
                ->rules(['nullable']),

            // Police details
            ImportColumn::make('police_name')
                ->label('Police Officer')
                ->guess(['PoliceName', 'PoliceOfficer'])
                ->example('Inspector Kwame Mensah')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('police_station')
                ->label('Police Station')
                ->guess(['PoliceStation'])
                ->example('Madina Police Station')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('police_contact')
                ->label('Police Contact')
                ->guess(['PoliceContact'])
                ->example('+233201234567')
                ->rules(['nullable', 'max:255']),

            
            ImportColumn::make('goaler')
                ->label('Goaler')
                ->guess(['Goaler'])
                ->example('Yes / No')
                ->castStateUsing(function (?string $state): ?int {
                    if (blank($state)) {
                        return 0;
                    }
                    $s = strtolower(trim($state));
                    return in_array($s, ['1', 'yes', 'y', 'true', 't']) ? 1 : 0;
                })
                ->rules(['nullable']),

            ImportColumn::make('goaler_document')
                ->label('Goaler Document')
                ->guess(['GoalerDocument'])
                ->example('["doc1.pdf"] or url')
                ->rules(['nullable']),

            // Transfer details
            ImportColumn::make('transferred_in')
                ->label('Transferred In')
                ->guess(['TransferredIn'])
                ->example('Yes / No')
                ->castStateUsing(function (?string $state): ?int {
                    if (blank($state)) {
                        return 0;
                    }
                    $s = strtolower(trim($state));
                    return in_array($s, ['1', 'yes', 'y', 'true', 't']) ? 1 : 0;
                })
                ->rules(['nullable']),

            ImportColumn::make('station_transferred_from_id')
                ->label('Station Transferred From (id/name)')
                ->guess(['StationTransferredFrom', 'StationTransferredFromId'])
                ->example('2 or Kumasi Central')
                ->rules(['nullable']),

            ImportColumn::make('date_transferred_in')
                ->label('Date Transferred In')
                ->guess(['DateTransferredIn'])
                ->example('2025-06-20')
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return \Carbon\Carbon::parse($state)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->rules(['nullable', 'date']),

            ImportColumn::make('transferred_out')
                ->label('Transferred Out')
                ->guess(['TransferredOut'])
                ->example('Yes / No')
                ->castStateUsing(function (?string $state): ?int {
                    if (blank($state)) {
                        return 0;
                    }
                    $s = strtolower(trim($state));
                    return in_array($s, ['1', 'yes', 'y', 'true', 't']) ? 1 : 0;
                })
                ->rules(['nullable']),

            ImportColumn::make('station_transferred_to_id')
                ->label('Station Transferred To (id/name)')
                ->guess(['StationTransferredTo', 'StationTransferredToId'])
                ->example('3 or Tamale Central')
                ->rules(['nullable']),

            ImportColumn::make('date_transferred_out')
                ->label('Date Transferred Out')
                ->guess(['DateTransferredOut'])
                ->example('2025-07-01')
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return \Carbon\Carbon::parse($state)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->rules(['nullable', 'date']),

            // Previous convictions (stored as json)
            ImportColumn::make('previous_convictions')
                ->label('Previous Convictions')
                ->guess(['PreviousConvictions'])
                ->example('Theft; Robbery')
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }
                    $parts = array_filter(array_map('trim', preg_split('/[;,]+/', $state)));
                    return empty($parts) ? null : json_encode(array_values($parts));
                })
                ->rules(['nullable']),

            ImportColumn::make('date_of_discharge')
                ->label('Date of Discharge')
                ->guess(['DateOfDischarge'])
                ->example('2025-08-01')
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return \Carbon\Carbon::parse($state)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->rules(['nullable', 'date']),

            ImportColumn::make('mode_of_discharge')
                ->label('Mode of Discharge')
                ->guess(['ModeOfDischarge'])
                ->example('Paroled')
                ->rules(['nullable', 'max:255']),

            ImportColumn::make('is_discharged')
                ->label('Discharged')
                ->guess(['IsDischarged', 'Discharged', 'is_discharged'])
                ->example('1 for discharged, 0 for not discharged')
                ->castStateUsing(function (?string $state): ?int {
                    if (blank($state)) {
                        return 0;
                    }
                    $s = strtolower(trim($state));
                    return in_array($s, ['1', 'yes', 'y', 'true', 't']) ? 1 : 0;
                })
                ->rules(['nullable']),
   
        ];
    }

    public function resolveRecord(): ?Inmate
    {
         // Get values from options (set during import action)
        $stationId = $this->options['station_id'] ?? Auth::user()->station->id;
        $gender = $this->options['gender'] ?? Auth::user()->station->category;
        
         // Store sentence data 
    $this->sentenceData = [
            'sentence'          => $this->data['sentence'] ?? null,
            'total_sentence'    => $this->data['total_sentence'] ?? null,
            'offence'           => $this->data['offence'] ?? null,
            'court_of_committal'=> $this->data['court_of_committal'] ?? null,
            'date_of_sentence'  => $this->data['date_of_sentence'] ?? null,
            'epd'               => $this->data['epd'] ?? null,
            'lpd'               => $this->data['lpd'] ?? null,
        ];

  
        return Inmate::firstOrNew(
            ['serial_number' => $this->data['serial_number']],
            [
        'full_name' => $this->data['full_name'],
        'gender' => $gender,
        'married_status' => $this->data['married_status']?? null,
        'age_on_admission' => $this->data['age_on_admission'],
        'admission_date' => $this->data['admission_date'],
        'previously_convicted' => $this->data['previously_convicted'] ?? false,
        'previous_sentence' => $this->data['previous_sentence'] ?? null,
        'previous_offence' => $this->data['previous_offence'] ?? null,
        'previous_station_id' => $this->data['previous_station_id'] ?? null,
        'station_id' => $stationId,
        'cell_id' => $this->data['cell_id'] ?? null,
        'next_of_kin_name' => $this->data['next_of_kin_name'] ?? null,
        'next_of_kin_relationship' => $this->data['next_of_kin_relationship'] ?? null,
        'next_of_kin_contact' => $this->data['next_of_kin_contact'] ?? null,
        'religion' => $this->data['religion'] ?? null,
        'nationality' => $this->data['nationality'],
        'education_level' => $this->data['education_level']?? null,
        'occupation' => $this->data['occupation']?? null,
        'hometown' => $this->data['hometown']?? null,
        'tribe' => $this->data['tribe']?? null,
        'distinctive_marks' => $this->data['distinctive_marks']?? null,
        'part_of_the_body' => $this->data['part_of_the_body']?? null,
        'languages_spoken' => $this->data['languages_spoken']?? null,
        'disability' => $this->data['disability'] ?? false,
        'disability_type' => $this->data['disability_type']?? null,
        'police_name' => $this->data['police_name']?? null,
        'police_station' => $this->data['police_station']?? null,
        'police_contact' => $this->data['police_contact']?? null,
        'goaler' => $this->data['goaler']   ?? false,
        'goaler_document' => $this->data['goaler_document']?? null,
        'transferred_in' => $this->data['transferred_in'] ?? false,
        'station_transferred_from_id' => $this->data['station_transferred_from_id'] ?? null,
        'date_transferred_in' => $this->data['date_transferred_in'] ?? null,
        'transferred_out' => $this->data['transferred_out'] ?? false,
        'station_transferred_to_id' => $this->data['station_transferred_to_id'] ?? null,
        'date_transferred_out' => $this->data['date_transferred_out'] ?? null,
        'previous_convictions' => $this->data['previous_convictions'] ?? null,
        'date_of_discharge' => $this->data['date_of_discharge'] ?? null,
        'mode_of_discharge' => $this->data['mode_of_discharge'] ?? null,
        'is_discharged' => $this->data['is_discharged'] ?? false,
    ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your inmate import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

     protected function afterValidate(): void
    {
          // Clean sentence fields 
        unset(
            $this->data['sentence'],
            $this->data['total_sentence'],
            $this->data['offence'],
            $this->data['court_of_committal'],
            $this->data['date_of_sentence'],
            $this->data['epd'],
            $this->data['lpd']
        );
    }


     protected function beforeSave(): void
    {
        // These are handled in resolveRecord, but keeping for safety
        if (!isset($this->data['station_id'])) {
            $this->data['station_id'] = Auth::user()->station->id; 
        }
          
        if (!isset($this->data['gender'])) {
            $this->data['gender'] = Auth::user()->station->category;
        }

      if (isset($this->data['discharged']) || isset($this->data['is_discharged'])) {
            $this->data['is_discharged'] = true;
        } 


    }

  protected function afterCreate(): void
{
    $inmate = $this->record;

        if (!$inmate) {
            Logger::info('No inmate record found after import.');
            return;
        }

        $sentenceData = $this->sentenceData;

        // Skip if nothing to create
        if (!array_filter($sentenceData)) {
            Logger::info('Skipping sentence creation - empty sentence data.');
            return;
        }

        try {
            Sentence::create([
                'inmate_id'         => $inmate->id,
                'sentence'          => $sentenceData['sentence'] ?? 'N/A',
                'total_sentence'    => $sentenceData['sentence'] ?? null,
                'offence'           => $sentenceData['offence'],
                'court_of_committal'=> $sentenceData['court_of_committal'],
                'date_of_sentence'  => $sentenceData['date_of_sentence'] ?? now()->format('Y-m-d'),
                'EPD'               => $sentenceData['epd'],
                'LPD'               => $sentenceData['lpd'],
            ]);
        } catch (\Throwable $e) {
            Logger::error('Sentence creation failed', [
                'error' => $e->getMessage(),
                'sentence_data' => $sentenceData
            ]);
            throw $e;
        }
}
}

