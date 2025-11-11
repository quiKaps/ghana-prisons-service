<?php

namespace App\Filament\Imports;

use App\Models\RemandTrial;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class TrialImporter extends Importer
{
    protected static ?string $model = RemandTrial::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('cell_id')
            ->label('Block and Cell')
                ->rules(['nullable', 'max:255'])
                ->example('A-01'),
            ImportColumn::make('serial_number')
                ->requiredMapping()
                ->label('Serial Number')
                ->guess(['SerialNumber'])
                ->example('SN-2025-0001')
                ->rules(['required', 'max:255']),
            ImportColumn::make('full_name')
            ->label('Prisoner Name')
                ->requiredMapping()
                 ->guess(['PrisonerName'])
                ->example('John Doe')
                ->rules(['required', 'max:255']),
            ImportColumn::make('offense')
            ->label('Offence')
                ->requiredMapping()
                ->guess(['Offence'])
                ->example('Theft')
                ->rules(['required', 'max:255']),
          ImportColumn::make('admission_date')
                ->requiredMapping()
                ->label('Date of Admission')
                ->guess(['DateofAdmission'])
                ->example('2025-06-01')
                ->castStateUsing(function (?string $state): ?string {  // ← ?string
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return \Carbon\Carbon::parse($state)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->rules(['required', 'date']),
                    ImportColumn::make('age_on_admission')
                    ->label('Age on Admission')
                ->numeric()
                ->guess(['Age'])
                ->example('34')
                ->castStateUsing(function (?string $state): ?int {  // ← ?string
                    if (blank($state)) {
                        return null;
                    }
                    $state = preg_replace('/[^0-9]/', '', $state);
                    return intval($state);
                })
                ->rules(['nullable', 'integer']),
                        ImportColumn::make('court')
                        ->label('Court of Committal')
                            ->requiredMapping()
                            ->example('Accra District Court')
                            ->guess(['Court'])
                            ->rules(['required', 'max:255']),
                    ImportColumn::make('next_court_date')
                ->requiredMapping()
                ->label('Next Court Date')
                ->guess(['NextCourtDate'])
                ->example('2025-07-15')
                ->castStateUsing(function (?string $state): ?string {  // ← ?string
                    if (blank($state)) {
                        return null;
                    }
                    try {
                        return \Carbon\Carbon::parse($state)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->rules(['required', 'date']),
            ImportColumn::make('country_of_origin')
                ->requiredMapping()
                ->guess(['nationality'])
                ->label('Nationality')
                ->example('Ghana')
                ->rules(['required', 'max:255']),
            ImportColumn::make('police_station')
                 ->guess(['PoliceStation'])
                 ->label('Police Station')
                ->example('Madina Police Station')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('police_officer')
            ->label('Police Officer')
                 ->guess(['PoliceName'])
                ->example('Inspector Kwame Mensah')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('police_contact')
            ->label('Police Contact')
                 ->guess(['PoliceContact'])
                ->example('+233201234567')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('re_admission_date')
                ->example('2025-06-15')
                ->guess(['ReAdDate'])
                ->label('Re-Admission Date')
                ->castStateUsing(function (?string $state): ?string {  // ← ?string
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
            ImportColumn::make('is_discharged')
            ->label('Discharged')
                        ->guess(['is_discharged', 'discharged'])
                        ->example('1 for discharged, 0 for not discharged')
                         ->castStateUsing(function (?string $state): ?string {  // ← ?string
                if (blank($state)) {
                    return null;
                }
                
                if($state == "Yes"){
                    return 1;
                }else{
                    return 0;
                }
            })
                        ->rules(['nullable', 'max:255']),
            ImportColumn::make('mode_of_discharge')
                    ->label('Mode of Discharge')
                        ->example('Paroled')
                        ->rules(['nullable', 'max:255']),
                ImportColumn::make('date_of_discharge')
            ->example('2025-08-01')
            ->label('Date of Discharge')
            ->castStateUsing(function (?string $state): ?string {  // ← ?string
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
        ];
    }

    public function resolveRecord(): ?RemandTrial
    {   
        // Get values from options (set during import action)
        $stationId = $this->options['station_id'] ?? Auth::user()->station->id;
        $gender = $this->options['gender'] ?? Auth::user()->station->category;
        
        return RemandTrial::firstOrNew([
            'serial_number' => $this->data['serial_number'],
            'detention_type' => RemandTrial::TYPE_TRIAL,
        ], [
            'full_name' => $this->data['full_name'],
            'station_id' => $stationId,
            'station' => $stationId, 
            'gender' => $gender,
            'cell_id' => $this->data['cell_id'] ?? null,
            'offense' => $this->data['offense'],
            'admission_date' => $this->data['admission_date'],
            'age_on_admission' => $this->data['age_on_admission'], 
            'court' => $this->data['court'],
            'next_court_date' => $this->data['next_court_date'],
            'country_of_origin' => $this->data['country_of_origin'],
            'police_station' => $this->data['police_station'] ?? null,
            'police_officer' => $this->data['police_officer'] ?? null,
            'police_contact' => $this->data['police_contact'] ?? null,
            're_admission_date' => $this->data['re_admission_date'] ?? null,
            'mode_of_discharge' => $this->data['mode_of_discharge'] ?? null,
            'date_of_discharge' => $this->data['date_of_discharge'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your trial import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function beforeSave(): void
    {
        // These are handled in resolveRecord, but keeping for safety
        if (!isset($this->data['station_id'])) {
            $this->data['station_id'] = Auth::user()->station->id; // Changed from ->station()->id()
        }
        
        if (!isset($this->data['detention_type'])) {
            $this->data['detention_type'] = RemandTrial::TYPE_TRIAL;
        }
        
        if (!isset($this->data['gender'])) {
            $this->data['gender'] = Auth::user()->station->category;
        }

        if (isset($this->data['discharged']) || isset($this->data['is_discharged'])) {
            $this->data['is_discharged'] = true;
        } 
    }
}
