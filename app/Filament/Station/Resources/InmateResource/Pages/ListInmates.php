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
            Actions\CreateAction::make()
                ->label('Admit A Convict'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All')
                ->badge(Inmate::count()),
            'Recividists' => Tab::make('Recividists')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('previously_convicted', true))
                ->badge(Inmate::where('previously_convicted', true)->count()),
            'Convict On Trial' => Tab::make('Convict on Trial')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('goaler', true))
                ->badge(Inmate::where('goaler', true)->count()),
            'Condemn' => Tab::make('Condemn')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('sentences', function ($query) {
                    $query->where('sentence', 'death');
                }))
                ->badge(fn() => Inmate::whereHas('sentences', function ($query) {
                    $query->where('sentence', 'death');
                })->count()),
            'Manslaughter' => Tab::make('Manslaughter')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('sentences', function ($query) {
                    $query->where('offence', 'manslaughter');
                }))
                ->badge(fn() => Inmate::whereHas('sentences', function ($query) {
                    $query->where('offence', 'manslaughter');
                })->count()),
            'Murder' => Tab::make('Murder')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('sentences', function ($query) {
                    $query->where('offence', 'murder');
                }))
                ->badge(fn() => Inmate::whereHas('sentences', function ($query) {
                    $query->where('offence', 'murder');
                })->count()),
            'lifer' => Tab::make('Lifer')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('sentences', function ($query) {
                    $query->where('sentence', 'life');
                }))
                ->badge(fn() => Inmate::whereHas('sentences', function ($query) {
                    $query->where('sentence', 'life');
                })->count()),
            'others' => Tab::make('Others')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('sentences', function ($query) {
                    $query->whereNotIn('offence', [
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
                    ]);
                }))
                ->badge(fn() => Inmate::whereHas('sentences', function ($query) {
                    $query->whereNotIn('offence', [
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
                    ]);
                })->count()),

        ];
    }
}
