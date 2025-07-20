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
        // $today = now()->toDateString();
        // $tomorrow = now()->addDay()->toDateString();


        // $counts = Inmate::whereIn('EPD', [$today, $tomorrow])
        //     ->selectRaw('EPD, COUNT(*) as count')
        //     ->groupBy('EPD')
        //     ->pluck('count', 'EPD');



        return [
            'today' => Tab::make('Today')
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
