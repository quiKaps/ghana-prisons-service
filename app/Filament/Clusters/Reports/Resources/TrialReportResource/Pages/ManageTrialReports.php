<?php

namespace App\Filament\Clusters\Reports\Resources\TrialReportResource\Pages;

use Filament\Actions;
use App\Enum\DetentionTypeEnum;
use Filament\Actions\ExportAction;
use App\Filament\Exports\TrialExporter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Models\Export;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Clusters\Reports\Resources\TrialReportResource;

class ManageTrialReports extends ManageRecords
{
    protected static string $resource = TrialReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
           ExportAction::make('export-trial')
                ->exporter(TrialExporter::class)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('detention_type', DetentionTypeEnum::TRIAL))
                ->label('Export Trial Report')
                ->fileName(fn (Export $export): string => "trial-report{$export->getKey()}.csv")
                ->color('warning'),
        ];
    }
}
