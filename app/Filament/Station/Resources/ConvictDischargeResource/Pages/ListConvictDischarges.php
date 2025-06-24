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



    public function getTabs(): array
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $counts = Inmate::whereIn('lpd', [$today, $tomorrow])
            ->selectRaw('lpd, COUNT(*) as count')
            ->groupBy('lpd')
            ->pluck('count', 'lpd');

        return [
            'Today' => Tab::make('Today')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('lpd', $today))
                ->badge(fn() => $counts->get($today, 0)),
            'Incoming Discharge' => Tab::make('Incoming Discharge')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('lpd', $tomorrow))
                ->badge(fn() => $counts->get($tomorrow, 0)),
        ];
    }
}
