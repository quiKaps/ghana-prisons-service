<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;

class ViewInmate extends Page implements HasInfolists
{

    use InteractsWithInfolists;

    public int $id = 0;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.station.pages.view-inmate';

    public function viewInmateInfolist(Infolist $infolist): Infolist
    {
        $inmate = \App\Models\Inmate::find($this->id);

        return $infolist
            ->record($inmate)
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('category.name'),
                // ...
            ]);
    }
}
