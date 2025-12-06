<?php

namespace App\Filament\Station\Resources\ReportResource\Pages;

use Filament\Actions;
use App\Enum\DetentionTypeEnum;
use Filament\Actions\ExportAction;
use App\Filament\Exports\TrialExporter;
use App\Filament\Exports\InmateExporter;
use App\Filament\Exports\RemandExporter;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Models\Export;
use App\Filament\Station\Resources\ReportResource;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('export-inmates')
                ->exporter(InmateExporter::class)
                ->label('Export Convicts Report')
                ->fileName(fn (Export $export): string => "convicts-report{$export->getKey()}.csv")
                ->color('green'),
            ExportAction::make('export-remand')
                ->exporter(RemandExporter::class)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('detention_type', DetentionTypeEnum::REMAND))
                ->label('Export Remand Report')
                ->fileName(fn (Export $export): string => "remands-report-{$export->getKey()}.csv")
                ->color('primary'),
            ExportAction::make('export-trial')
                ->exporter(TrialExporter::class)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('detention_type', DetentionTypeEnum::TRIAL))
                ->label('Export Trial Report')
                ->fileName(fn (Export $export): string => "trial-report{$export->getKey()}.csv")
                ->color('warning'),
        ];
    }
}
