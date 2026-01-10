<?php

namespace App\Filament\Clusters\Reports\Resources\ConvictReportResource\Pages;

use Filament\Actions;
use Filament\Actions\ExportAction;
use App\Filament\Exports\InmateExporter;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Exports\Models\Export;
use App\Filament\Clusters\Reports\Resources\ConvictReportResource;

class ListConvictReports extends ListRecords
{
    protected static string $resource = ConvictReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('export-inmates')
                ->exporter(InmateExporter::class)
                ->label('Export Convicts Report')
                ->fileName(fn (Export $export): string => "convicts-report{$export->getKey()}.csv")
                ->color('green'),
        ];
    }
}
