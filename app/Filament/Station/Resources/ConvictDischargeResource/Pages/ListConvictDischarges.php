<?php

namespace App\Filament\Station\Resources\ConvictDischargeResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Station\Resources\ConvictDischargeResource;

class ListConvictDischarges extends ListRecords
{
    protected static string $resource = ConvictDischargeResource::class;

    protected ?string $heading = 'Convict Discharges';

    protected ?string $subheading = 'Manage and track convicts discharges.';

    public function getTabs(): array
    {
        return [
            'today' => Tab::make('All Dicharges Today')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->allToday()
                )
                ->badge(Inmate::allToday()->count()),
            'one-third-remission' => Tab::make('1/3 Remission')
                ->modifyQueryUsing(
                fn(Builder $query) => $query->withEpdToday()
                )
                ->badge(Inmate::withEpdToday()->count()),
            'tomorrow' => Tab::make("Tomorrow")
                ->modifyQueryUsing(
                fn(Builder $query) => $query->withEpdTomorrow()
                )
                ->badge(Inmate::withEpdTomorrow()->count()),
            'thisMonth' => Tab::make('Next Month')
                ->modifyQueryUsing(fn(Builder $query) => $query->withEpdNextMonth())
                ->badge(Inmate::withEpdNextMonth()->count()),
            'allPrevious' => Tab::make("All Discharges")
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                $query->where('is_discharged', true)
                    ->orderByDesc('created_at')
                )
                ->badge(Inmate::where('is_discharged', true)
                ->orderByDesc('created_at')
                    ->count()),
        ];
    }
}
