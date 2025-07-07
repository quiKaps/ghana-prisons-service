<?php

namespace App\Filament\Station\Resources\RemandResource\Pages;

use Filament\Actions;
use App\Models\RemandTrial;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Station\Resources\RemandResource;
use App\Filament\Station\Resources\RemandTrialResource\Pages\CreateRemandTrial;

class ListRemands extends ListRecords
{
    protected static string $resource = RemandResource::class;

    public function getTitle(): string
    {
        return 'All Prisoners on Remand';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage all prisoners on remand';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Admit Remand')
                ->url(CreateRemandTrial::getUrl())
                ->icon('heroicon-o-user-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn(Builder $query) => $query
                ->where('is_discharged',  false)
                ->where('next_court_date', '>=', now()))
                ->badge(RemandTrial::where('detention_type', 'remand')
                    ->where('is_discharged', false)->count()),

            'foreigner' => Tab::make('Foreigners')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('is_discharged', false)
                    ->where('detention_type', 'remand')
                    ->where('country_of_origin', '!=', 'ghana'))
                ->badge(RemandTrial::where('is_discharged', false)
                    ->where('detention_type', 'remand')
                    ->where('country_of_origin', '!=', 'ghana')
                    ->count()),

            'expireWarrants' => Tab::make('Expired Warrants')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('is_discharged', false)
                    ->where('detention_type', 'remand')
                    ->where('next_court_date', '<', now()))
                ->badge(RemandTrial::where('is_discharged', false)
                    ->where('detention_type', 'remand')
                    ->where('next_court_date', '<', now())
                    ->count()),

            'escape' => Tab::make('Escapees')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('is_discharged', true)
                    ->where('detention_type', 'remand')
                    ->where('mode_of_discharge', 'escape'))
                ->badge(RemandTrial::where('is_discharged', true)
                    ->where('detention_type', 'remand')
                    ->where('mode_of_discharge',  'escape')
                    ->count()),

            'discharged' => Tab::make('Discharged')
                ->modifyQueryUsing(fn(Builder $query) => $query
                    ->where('is_discharged', true)
                    ->where('detention_type', 'remand')
                    ->where('mode_of_discharge', '!=', 'escape'))
                ->badge(RemandTrial::where('is_discharged', true)
                    ->where('detention_type', 'remand')
                    ->where('mode_of_discharge', '!=', 'escape')
                    ->count()),


        ];
    }
}
