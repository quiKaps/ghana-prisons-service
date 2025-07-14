<?php

namespace App\Filament\HQ\Resources\TrialResource\Pages;

use Filament\Actions;
use App\Models\RemandTrial;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\HQ\Resources\TrialResource;

class ListTrials extends ListRecords
{
    protected static string $resource = TrialResource::class;

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
                ->modifyQueryUsing(fn(Builder $query) => $query->trial())
                ->badge(\App\Models\RemandTrial::trial()->count()),
            'upcoming' => Tab::make("Upcoming Court Date")
                ->icon('heroicon-m-clock')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->active(RemandTrial::TYPE_REMAND)
                    ->where('next_court_date', today()))
                ->badge(\App\Models\RemandTrial::active(RemandTrial::TYPE_REMAND)
                    ->where('next_court_date', today())
                    ->count()),
            'foreigner' => Tab::make('Foreigners')
                ->modifyQueryUsing(fn(Builder $query) => $query->foreigners(RemandTrial::TYPE_TRIAL)->where('next_court_date', '>', today()))
                ->badge(\App\Models\RemandTrial::foreigners(RemandTrial::TYPE_TRIAL)->where('next_court_date', '>', today())->count()),

            'escape' => Tab::make('Escapees')
                ->modifyQueryUsing(fn(Builder $query) => $query->escapees(RemandTrial::TYPE_TRIAL))
                ->badge(\App\Models\RemandTrial::escapees(RemandTrial::TYPE_TRIAL)->count()),

            'discharged' => Tab::make('Discharged')
                ->modifyQueryUsing(fn(Builder $query) => $query->discharged(RemandTrial::TYPE_TRIAL))
                ->badge(\App\Models\RemandTrial::discharged(RemandTrial::TYPE_TRIAL)->count()),
        ];
    }
}
