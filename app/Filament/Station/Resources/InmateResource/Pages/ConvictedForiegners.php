<?php

namespace App\Filament\Station\Resources\InmateResource\Pages;

use App\Filament\Station\Resources\InmateResource;
use Filament\Resources\Pages\Page;

class ConvictedForiegners extends Page
{
    protected static string $resource = InmateResource::class;

    protected static string $view = 'filament.station.resources.inmate-resource.pages.convicted-foriegners';

    protected static ?string $navigationGroup = 'Trials';

    protected static ?string $navigationLabel = 'Foreigners - Convicts';

    protected static ?string $title = 'Convicted Foreigners';
}
