<?php

namespace App\Filament\Station\Resources\TrialResource\Pages;

use Filament\Actions;
use App\Models\RemandTrial;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Station\Resources\TrialResource;
use App\Filament\Station\Resources\RemandTrialResource\Pages\CreateRemandTrial;

class ListTrials extends ListRecords
{
    protected static string $resource = TrialResource::class;

    public function getTitle(): string
    {
        return 'All Prisoners on Trial';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage all prisoners on trial';
    }



    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Admit Trial')
                ->url(CreateRemandTrial::getUrl())
                ->icon('heroicon-o-user-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn(Builder $query) => $query->active(RemandTrial::TYPE_TRIAL))
                ->badge(\App\Models\RemandTrial::active(RemandTrial::TYPE_TRIAL)->count()),

            'upcoming' => Tab::make("Upcoming Court Date")
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
