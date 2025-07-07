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
            'all' => Tab::make('All')
                ->modifyQueryUsing(fn(Builder $query) => $query
                ->where('detention_type', 'trial')
                ->where('is_discharged', false))
                ->badge(RemandTrial::where('detention_type', 'trial')
                    ->where('is_discharged', false)->count()),

            'foreigner' => Tab::make('Foreigners')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('is_discharged', false)
                    ->where('detention_type', 'trial')
                    ->where('country_of_origin', '!=', 'ghana'))
                ->badge(RemandTrial::where('is_discharged', false)
                    ->where('detention_type', 'trial')
                    ->where('country_of_origin', '!=', 'ghana')
                    ->count()),

            'escape' => Tab::make('Escapees')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('is_discharged', true)
                    ->where('detention_type', 'trial')
                    ->where('mode_of_discharge', 'escape'))
                ->badge(RemandTrial::where('is_discharged', true)
                    ->where('detention_type', 'trial')
                    ->where('mode_of_discharge',  'escape')
                    ->count()),

            'discharged' => Tab::make('Discharged')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('is_discharged', true)
                    ->where('detention_type', 'trial')
                    ->where('mode_of_discharge', '!=', 'escape'))
                ->badge(RemandTrial::where('is_discharged', true)
                    ->where('detention_type', 'trial')
                    ->where('mode_of_discharge', '!=', 'escape')
                    ->count()),

        ];
    }
}
