<?php

namespace App\Filament\Exports;

use App\Models\Inmate;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InmateExporter extends Exporter
{
    protected static ?string $model = Inmate::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('prisoner_picture'),
            ExportColumn::make('serial_number'),
            ExportColumn::make('full_name'),
            ExportColumn::make('gender'),
            ExportColumn::make('married_status'),
            ExportColumn::make('age_on_admission'),
            ExportColumn::make('admission_date'),
            ExportColumn::make('previously_convicted'),
            ExportColumn::make('previous_sentence'),
            ExportColumn::make('previous_offence'),
            ExportColumn::make('previous_station_id'),
            ExportColumn::make('station.name'),
            ExportColumn::make('cell_id'),
            ExportColumn::make('court_of_committal'),
            ExportColumn::make('next_of_kin_name'),
            ExportColumn::make('next_of_kin_relationship'),
            ExportColumn::make('next_of_kin_contact'),
            ExportColumn::make('religion'),
            ExportColumn::make('nationality'),
            ExportColumn::make('education_level'),
            ExportColumn::make('occupation'),
            ExportColumn::make('hometown'),
            ExportColumn::make('tribe'),
            ExportColumn::make('distinctive_marks'),
            ExportColumn::make('part_of_the_body'),
            ExportColumn::make('languages_spoken'),
            ExportColumn::make('disability'),
            ExportColumn::make('disability_type'),
            ExportColumn::make('police_name'),
            ExportColumn::make('police_station'),
            ExportColumn::make('police_contact'),
            ExportColumn::make('goaler'),
            ExportColumn::make('goaler_document'),
            ExportColumn::make('transferred_in'),
            ExportColumn::make('station_transferred_from_id'),
            ExportColumn::make('date_transferred_in'),
            ExportColumn::make('transferred_out'),
            ExportColumn::make('stationTransferredTo.name'),
            ExportColumn::make('date_transferred_out'),
            ExportColumn::make('previous_convictions'),
            ExportColumn::make('date_of_discharge'),
            ExportColumn::make('mode_of_discharge'),
            ExportColumn::make('is_discharged'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your inmate export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
