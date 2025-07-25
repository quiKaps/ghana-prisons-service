<?php

namespace App\Filament\HQ\Resources\RemandResource\Pages;

use Filament\Actions;
use App\Models\RemandTrial;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\HQ\Resources\RemandResource;

class ListRemands extends ListRecords
{
    protected static string $resource = RemandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {

        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn(Builder $query) => $query->active(RemandTrial::TYPE_REMAND))
                ->badge(\App\Models\RemandTrial::active(RemandTrial::TYPE_REMAND)->count()),

            'upcoming' => Tab::make("Upcoming Court Date")
                ->modifyQueryUsing(fn(Builder $query) => $query->active(RemandTrial::TYPE_REMAND)
                    ->where('next_court_date', today()))
                ->badge(\App\Models\RemandTrial::active(RemandTrial::TYPE_REMAND)
                    ->where('next_court_date', today())
                    ->count()),

            'foreigner' => Tab::make('Foreigners')
                ->modifyQueryUsing(fn(Builder $query) => $query->foreigners(RemandTrial::TYPE_REMAND)->where('next_court_date', '>', today()))
                ->badge(\App\Models\RemandTrial::foreigners(RemandTrial::TYPE_REMAND)->where('next_court_date', '>', today())->count()),

            'expireWarrants' => Tab::make('Expired Warrants')
                ->modifyQueryUsing(fn(Builder $query) => $query->expiredWarrants(RemandTrial::TYPE_REMAND))
                ->badge(\App\Models\RemandTrial::expiredWarrants(RemandTrial::TYPE_REMAND)->count()),

            'escape' => Tab::make('Escapees')
                ->modifyQueryUsing(fn(Builder $query) => $query->escapees(RemandTrial::TYPE_REMAND))
                ->badge(\App\Models\RemandTrial::escapees(RemandTrial::TYPE_REMAND)->count()),

            'discharged' => Tab::make('Discharged')
                ->modifyQueryUsing(fn(Builder $query) => $query->discharged(RemandTrial::TYPE_REMAND))
                ->badge(\App\Models\RemandTrial::discharged(RemandTrial::TYPE_REMAND)->count()),
        ];
    }
}
