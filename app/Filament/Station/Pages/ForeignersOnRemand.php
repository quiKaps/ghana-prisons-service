<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;

class ForeignersOnRemand extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.station.pages.foreigners-on-remand';

    protected static ?string $navigationGroup = 'Remand';
}
