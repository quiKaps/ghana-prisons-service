<?php

namespace App\Filament\HQ\Widgets;

use App\Traits\Has30DayTrend;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use Has30DayTrend;
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;


    protected function getHeading(): ?string
    {
        return Auth::user()?->station->name . " Insights";
    }

    protected function getDescription(): ?string
    {
        return 'Key metrics on admissions, discharges, and custody trends across your facility.';
    }

    protected function getStats(): array
    {

        $startDate = $this->filters['startDate'];

        $endDate = $this->filters['endDate'];

        $station = $this->filters['station_id'];

        // \App\Models\Inmate::when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
        // ->when($endDate, fn($query) => $query->whereDate('created_at', '>=', $endDate))
        // ->when($station, fn($query) => $query->whereDate('station_id', $station))
        // ->active()->orWhere('mode_of_discharge', 'escape')->count() +
        // \App\Models\RemandTrial::when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
        // ->when($endDate, fn($query) => $query->whereDate('created_at', '>=', $endDate))
        // ->when($station, fn($query) => $query->whereDate('station_id', $station))->where('is_discharged', false) // Include all active inmates
        // ->orWhere('mode_of_discharge', 'escape')->count()

        return [

            // 📂 Custody Overview
            Stat::make(
                'Total locked up',
                number_format(
                    // Improved filtering with proper date range and query grouping to avoid logic issues
                    \App\Models\Inmate::when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($station, fn($query) => $query->where('station_id', $station))
                        ->where(function ($query) {
                            $query->active()->orWhere('mode_of_discharge', 'escape');
                        })
                        ->count()
                        +
                        \App\Models\RemandTrial::when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($station, fn($query) => $query->where('station_id', $station))
                        ->where(function ($query) {
                            $query->where('is_discharged', false)
                                ->orWhere('mode_of_discharge', 'escape');
                        })
                        ->count()
                )
            )
                ->description("Total number of prisoners in custody")
                ->icon('heroicon-o-lock-closed')
                ->color('success')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                    $q->when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where(function ($query) {
                            $query->active()->orWhere('mode_of_discharge', 'escape');
                        })
                ))->chartColor('green'),
            //convicts
            Stat::make(
                'Convicts',
                number_format(
                    \App\Models\Inmate::when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where(function ($query) {
                            $query->active()->orWhere('mode_of_discharge', 'escape');
                        })
                        ->count()
                )
            )
                ->description('Convicted prisoners currently in custody')
                ->icon('heroicon-o-user-group')
                ->color('info')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                    $q->when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where('is_discharged', false)
                ))->chartColor('blue')
                ->extraAttributes([
                    'tooltip' => 'Includes only sentenced prisoners not discharged or transferred out.',
                ]),
            //convict ends here

            //remand
            Stat::make(
                'Active Remands',
                number_format(
                    \App\Models\RemandTrial::remand()
                        ->when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where('next_court_date', '>=', today())
                        ->count()
                )
            )
                ->description("Prisoners currently held on remand")
                ->icon('heroicon-o-scale')
                ->color('warning')
                ->chart($this->get30DayTrendData(
                    \App\Models\RemandTrial::class,
                    fn($q) =>
                $q->when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                    ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                    ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                    ->where('detention_type', 'remand')
                    ->where('is_discharged', false)
                ))
                ->chartColor('warning'),

            Stat::make(
                'Trial',
                number_format(
                    \App\Models\RemandTrial::trial()
                        ->when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where(function ($query) {
                            $query->where('is_discharged', false)
                                ->orWhere('mode_of_discharge', 'escape');
                        })
                        ->count()
                )
            )
                ->description("Prisoners currently on trial")
                ->icon('heroicon-o-briefcase')
                ->color('info')
                ->chart($this->get30DayTrendData(
                    \App\Models\RemandTrial::class,
                    fn($q) =>
                $q->when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                    ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                    ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                    ->where('detention_type', 'trial')
                    ->where('is_discharged', false)
                ))
                ->chartColor('info'),

            // 📂 Alerts & Exceptions
            Stat::make(
                'Expired Warrants',
                number_format(
                    \App\Models\RemandTrial::remand()
                        ->when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->whereDate('next_court_date', '<', today())
                        ->count()
                )
            )
                ->description("Remand prisoners with expired court warrants")
                ->icon('heroicon-o-exclamation-circle')
                ->label('Expired Warrants')
                ->color('danger')
                ->chart($this->get30DayTrendData(
                    \App\Models\RemandTrial::class,
                    fn($q) =>
                $q->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                    ->where('detention_type', 'remand')
                        ->where('is_discharged', false)
                        ->whereDate('next_court_date', '<', today())
                ))
                ->chartColor('danger'),

            Stat::make(
                'Escapees',
                number_format(
                    \App\Models\Inmate::when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where('is_discharged', true)
                        ->whereHas(
                            'discharge',
                            fn($q) => $q->where('discharge_type', 'escape')
                        )
                        ->count()
                        +
                        \App\Models\RemandTrial::when($this->filters['startDate'] ?? null, fn($query, $startDate) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($this->filters['endDate'] ?? null, fn($query, $endDate) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where('is_discharged', true)
                        ->where('mode_of_discharge', 'escape')
                        ->count()
                )
            )
                ->description("Prisoners who have escaped custody")
                ->icon('heroicon-o-flag')
                ->color('danger')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                $q->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                    ->whereHas(
                        'discharge',
                    fn($q) => $q->where('discharge_type', 'escape')
                )
                ))
                ->chartColor('danger'),

            // 📂 Daily Activity
            Stat::make(
                'Discharged Today',
                number_format(
                    \App\Models\Inmate::when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->whereHas(
                            'discharge',
                            fn($q) => $q->whereDate('discharge_date', today())
                        )
                        ->count()
                        +
                        \App\Models\RemandTrial::when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where('is_discharged', true)
                        ->whereDate('date_of_discharge', today())
                        ->count()
                )
            )
                ->description("Prisoners discharged from custody today")
                ->icon('heroicon-o-arrow-up-right')
                ->color('warning')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                $q->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                    ->whereHas(
                        'discharge',
                    fn($q) => $q->whereDate('discharge_date', today())
                )
                ))
                ->chartColor('warning')
                ->extraAttributes([
                    'tooltip' => 'Includes both convicts and remand prisoners discharged today.',
                ]),

            Stat::make(
                'Admissions Today',
                number_format(
                    \App\Models\Inmate::when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->whereDate('created_at', today())
                        ->count()
                        +
                        \App\Models\RemandTrial::when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->whereDate('created_at', today())
                        ->count()
                )
            )
                ->description("New prisoners admitted today")
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                $q->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                    ->whereDate('created_at', today())
                ))
                ->chartColor('success')
                ->extraAttributes([
                    'tooltip' => 'Includes all new admissions across remand and convict categories.',
                ]),

            // 📂 Daily Activity
            Stat::make(
                'Transferred Today',
                number_format(
                    \App\Models\Inmate::when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                        ->where('transferred_out', true)
                        ->where('date_transferred_out', today())
                        ->count()
                )
            )
                ->description("Prisoners transferred from the facility today")
                ->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->color('green')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                $q->when($this->filters['station_id'] ?? null, fn($query, $station) => $query->where('station_id', $station))
                    ->where('transferred_out', true)
                    ->where('date_transferred_out', today())
                ))
                ->chartColor('green')
                ->extraAttributes([
                    'tooltip' => 'Includes all prisoners transferred from this facility today.',
                ]),


        ];
    }
}
