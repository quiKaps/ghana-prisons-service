<?php

namespace App\Filament\Station\Widgets;

use Filament\Widgets\ChartWidget;

class RemandTrialsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected static ?int $sort = 2;


    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
