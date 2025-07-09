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


            'Active' => Tab::make('Active')
                ->modifyQueryUsing(fn(Builder $query) => $query->active())
                ->badge(fn() => Inmate::active()->count()),

            'foreigners' => Tab::make('Foreigners')
                ->modifyQueryUsing(fn(Builder $query) => $query->active()
                    ->where('nationality', '!=', 'ghana'))
                ->badge(fn() => Inmate::active()
                    ->where('nationality', '!=', 'ghana')
                    ->count()),

            'escape' => Tab::make('Escapees')
                ->modifyQueryUsing(fn(Builder $query) => $query->escapees())
                ->badge(\App\Models\Inmate::escapees()->count()),

            'Recidivists' => Tab::make('Recidivists')
                ->modifyQueryUsing(fn(Builder $query) => $query->recidivists())
                ->badge(fn() => Inmate::recidivists()->count()),

            'Convict on Trial' => Tab::make('On Trial')
                ->modifyQueryUsing(fn(Builder $query) => $query->convictOnTrial())
                ->badge(fn() => Inmate::convictOnTrial()->count()),

            'Condemn' => Tab::make('Condemn')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('sentence', 'death')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('sentence', 'death')->count()
                ),

            'Manslaughter' => Tab::make('Manslaughter')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('offence', 'manslaughter')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('offence', 'manslaughter')->count()
                ),

            'Murder' => Tab::make('Murder')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('offence', 'murder')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('offence', 'murder')->count()
                ),

            'Robbery' => Tab::make('Robbery')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('offence', 'robbery')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('offence', 'robbery')->count()
                ),

            'Lifer' => Tab::make('Lifer')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->withSentenceType('sentence', 'life')
                )
                ->badge(
                    fn() =>
                    Inmate::withSentenceType('sentence', 'life')->count()
                ),

            'Others' => Tab::make('Others')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                $query->withoutOffences()
                )
                ->badge(
                    fn() =>
                Inmate::withoutOffences()->count()
                ),


        ];
    }
}
