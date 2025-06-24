<?php

namespace App\Filament\Station\Pages;

use Filament\Pages\Page;

class ConvictedEscapees extends Page
{

    protected static string $view = 'filament.station.pages.convicted-escapees';

    protected static ?string $navigationGroup = 'Escapees';

    protected static ?string $navigationLabel = 'Escaped List - Convict';
}
