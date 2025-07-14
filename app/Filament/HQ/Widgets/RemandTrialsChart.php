<?php

namespace App\Filament\HQ\Widgets;

use Flowframe\Trend\Trend;
use App\Models\RemandTrial;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class RemandTrialsChart extends ChartWidget
{
    protected static ?string $heading = 'Year-to-Date Remand Trends';

    protected static ?string $description = 'Monthly distribution of remand & trials admissions from January to December.';

    protected static ?int $sort = 2;



    protected function getData(): array
    {
        $data = Trend::model(RemandTrial::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Remand & Trial Admissions',
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
