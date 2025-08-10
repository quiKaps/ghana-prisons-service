<?php

namespace App\Filament\Station\Resources\TransfersResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Station\Resources\TransfersResource;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransfersResource::class;

    public function getTitle(): string
    {
        return 'Prison Transfers';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage all transfers in and out of the facility';
    }

    public function getTabs(): array
    {
        return [
            'transfersIn' => Tab::make('Transfers In')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->where('transferred_in',  true)
                )
                ->badge(Inmate::where('transferred_in', true)
                    ->count()),

            'transferOut' => Tab::make('Transfers Out')
                ->modifyQueryUsing(fn(Builder $query) => $query
                ->where('transferred_out', true)
                ->orderBy('date_transferred_out', 'DESC'))

                ->badge(Inmate::where('transferred_out', true)
                    ->count()),
        ];
    }
}
