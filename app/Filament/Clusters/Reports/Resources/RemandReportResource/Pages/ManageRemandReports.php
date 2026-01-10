<?php

namespace App\Filament\Clusters\Reports\Resources\RemandReportResource\Pages;

use App\Enum\DetentionTypeEnum;
use Filament\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\RemandExporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Clusters\Reports\Resources\RemandReportResource;

class ManageRemandReports extends ManageRecords
{
    protected static string $resource = RemandReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
           ExportAction::make('export-remand')
                ->exporter(RemandExporter::class)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('detention_type', DetentionTypeEnum::REMAND->value))
                ->label('Export Remand Report')
                ->fileName(fn (Export $export): string => "remands-report-{$export->getKey()}.csv")
                ->color('primary'),
        ];
    }
}
