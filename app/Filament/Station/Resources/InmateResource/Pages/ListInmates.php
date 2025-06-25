<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use App\Filament\Station\Resources\InmateResource;
use App\Models\Inmate;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInmates extends ListRecords
{
    protected static string $resource = InmateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All')
                ->badge(Inmate::count()),
            'Recividists' => Tab::make('Recividists')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('previously_convicted', true))
                ->badge(Inmate::count()),
            'Condemn' => Tab::make('Condemn')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('offence', 'condemn'))
                ->badge(fn() => Inmate::where('offence', 'condemn')->count()),
            'Manslaughter' => Tab::make('Manslaughter')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('offence', 'manslaughter'))
                ->badge(fn() => Inmate::where('offence', 'manslaughter')->count()),
            'Murder' => Tab::make('Murder')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('offence', 'murder'))
                ->badge(fn() => Inmate::where('offence', 'murder')->count()),
            'Lifer' => Tab::make('Lifer')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('offence', 'lifer'))
                ->badge(fn() => Inmate::where('offence', 'lifer')->count()),
            'Others' => Tab::make('Others')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotIn('offence', [
                    'assault',
                    'causing_harm',
                    'defilement',
                    'defrauding',
                    'manslaughter',
                    'murder',
                    'robbery',
                    'stealing',
                    'unlawful_damage',
                    'unlawful_entry',
                ]))
                ->badge(fn() => Inmate::whereNotIn('offence', [
                    'assault',
                    'causing_harm',
                    'defilement',
                    'defrauding',
                    'manslaughter',
                    'murder',
                    'robbery',
                    'stealing',
                    'unlawful_damage',
                    'unlawful_entry',
                ])->count()),
        ];
    }
}
