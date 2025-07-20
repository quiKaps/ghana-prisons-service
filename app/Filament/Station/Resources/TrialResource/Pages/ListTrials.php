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
                ->modifyQueryUsing(fn(Builder $query) => $query->trial())
                ->badge(\App\Models\RemandTrial::trial()->count()),

            'today_admission' => Tab::make("Today's Admissions")
                ->icon('heroicon-m-clock')
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->active(RemandTrial::TYPE_REMAND)
                    ->whereDate('created_at', today()))
                ->badge(\App\Models\RemandTrial::active(RemandTrial::TYPE_REMAND)
                    ->whereDate('created_at', today())
                    ->count()),

            'upcoming' => Tab::make("Today's Court Hearing")
                ->icon('heroicon-m-clock')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->active(RemandTrial::TYPE_REMAND)
                ->whereDate('next_court_date', today()))
                ->badge(\App\Models\RemandTrial::active(RemandTrial::TYPE_REMAND)
                    ->whereDate('next_court_date', today())
                    ->count()),

            'today_discharge' => Tab::make("Today's Discharges")
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->discharged(RemandTrial::TYPE_REMAND)
                    ->whereDate('date_of_discharge', today()))
                ->badge(\App\Models\RemandTrial::discharged(RemandTrial::TYPE_REMAND)
                    ->whereDate('date_of_discharge', today())
                    ->count()),

            'foreigner' => Tab::make('Foreigners')
                ->modifyQueryUsing(fn(Builder $query) => $query->foreigners(RemandTrial::TYPE_TRIAL)->where('next_court_date', '>', today()))
                ->badge(\App\Models\RemandTrial::foreigners(RemandTrial::TYPE_TRIAL)->whereDate('next_court_date', '>', today())->count()),

            'escape' => Tab::make('Escapees')
                ->modifyQueryUsing(fn(Builder $query) => $query->escapees(RemandTrial::TYPE_TRIAL))
                ->badge(\App\Models\RemandTrial::escapees(RemandTrial::TYPE_TRIAL)->count()),

            'discharged' => Tab::make('Discharged')
                ->modifyQueryUsing(fn(Builder $query) => $query->discharged(RemandTrial::TYPE_TRIAL)->orderByDesc('date_of_discharge'))
                ->badge(\App\Models\RemandTrial::discharged(RemandTrial::TYPE_TRIAL)->count()),
        ];
    }
}
