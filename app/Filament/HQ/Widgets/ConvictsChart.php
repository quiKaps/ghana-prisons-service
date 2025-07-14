<?php

namespace App\Filament\HQ\Widgets;

use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class ConvictsChart extends ChartWidget
{
    protected static ?string $heading = 'Year-to-Date Convict Trends';

    protected static ?string $description = 'Monthly distribution of convict admissions from January to December.';


    protected static ?int $sort = 3;


    protected function getData(): array
    {
        $data = Trend::model(\App\Models\Inmate::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Convict Admissions',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
