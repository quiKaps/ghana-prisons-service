<?php

namespace App\Filament\Exports;

use App\Models\RemandTrial;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TrialExporter extends Exporter
{
    protected static ?string $model = RemandTrial::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('station.name')
               ->label('Station'),
            ExportColumn::make('cell_id')
            ->label('Cell Number'),
            ExportColumn::make('serial_number')
                ->label('Serial Number'),
            ExportColumn::make('picture')
                ->label('Picture'),
            ExportColumn::make('full_name')
                ->label('Full Name'),
            ExportColumn::make('gender')
                ->label('Gender'),
            ExportColumn::make('offense')
                ->label('Offense'),
            ExportColumn::make('admission_date')
                ->label('Admission Date'),
            ExportColumn::make('age_on_admission')
                ->label('Age on Admission'),
            ExportColumn::make('court')
                ->label('Court'),
            ExportColumn::make('detention_type')
                ->label('Detention Type'),
            ExportColumn::make('next_court_date')
                ->label('Next Court Date'),
            ExportColumn::make('warrant')
                ->label('Warrant'),
            ExportColumn::make('country_of_origin')
                ->label('Country of Origin'),
            ExportColumn::make('police_station')
                ->label('Police Station'),
            ExportColumn::make('police_officer')
                ->label('Police Officer'),
            ExportColumn::make('police_contact')
                ->label('Police Contact'),
            ExportColumn::make('re_admission_date')
                ->label('Re-admission Date'),
            ExportColumn::make('is_discharged')
                ->label('Is Discharged'),
            ExportColumn::make('mode_of_discharge')
                ->label('Mode of Discharge'),
            ExportColumn::make('discharged_by')
                ->label('Discharged By'),
            ExportColumn::make('date_of_discharge')
                ->label('Date of Discharge'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your remand trial export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
