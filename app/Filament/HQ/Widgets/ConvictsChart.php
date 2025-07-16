<?php

namespace App\Filament\HQ\Widgets;

use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ConvictsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Year-to-Date Convict Trends';

    protected static ?string $description = 'Monthly distribution of convict admissions from January to December.';


    protected static ?int $sort = 3;

    public ?string $filter = 'today';


    protected function getData(): array
    {

        $startDate = $this->filters['startDate'];

        $endDate = $this->filters['endDate'];

        $station = $this->filters['station_id'];

        $activeFilter = $this->filter;

        $data = Trend::model(\App\Models\Inmate::class)
            ->between(
            start: $startDate ? Carbon::parse($startDate) : now()->startOfYear(),
            end: $endDate ? Carbon::parse($endDate) : now()->endOfYear(),
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

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
}
