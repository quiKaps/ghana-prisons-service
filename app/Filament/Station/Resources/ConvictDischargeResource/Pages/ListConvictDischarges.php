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

    protected ?string $subheading = 'Manage and track discharged prisoners.';

    public function getTabs(): array
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();


        $counts = Inmate::whereIn('lpd', [$today, $tomorrow])
            ->selectRaw('epd, COUNT(*) as count')
            ->groupBy('epd')
            ->pluck('count', 'lpd');

        return [
            'oneThirdRemission' => Tab::make('1/3rd Remission')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('epd', $today))
                ->badge(fn() => $counts->get($today, 0)),
            'special_discharge' => Tab::make('Special Discharge')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('lpd', $tomorrow))
                ->badge(fn() => $counts->get($tomorrow, 0)),
        ];
    }
}
