<?php

namespace App\Filament\Exports;

use App\Models\RemandTrial;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RemandExporter extends Exporter
{
    protected static ?string $model = RemandTrial::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('station_id'),
            ExportColumn::make('cell.id'),
            ExportColumn::make('serial_number'),
            ExportColumn::make('name'),
            ExportColumn::make('offense'),
            ExportColumn::make('admission_date'),
            ExportColumn::make('age_on_admission'),
            ExportColumn::make('court'),
            ExportColumn::make('detention_type'),
            ExportColumn::make('next_court_date'),
            ExportColumn::make('warrant'),
            ExportColumn::make('country_of_origin'),
            ExportColumn::make('police_station'),
            ExportColumn::make('police_officer'),
            ExportColumn::make('police_contact'),
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
