<?php

namespace App\Filament\Station\Widgets;

use App\Models\Inmate;
use App\Models\RemandTrial;
use App\Traits\Has30DayTrend;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use Has30DayTrend;

    protected static ?int $sort = 1;


    protected function getStats(): array
    {


        return [
            Stat::make(
                'Total locked up',
                number_format(\App\Models\Inmate::where('is_discharged', false)->count() + \App\Models\RemandTrial::where('is_discharged', false)->count())
            )
                ->chart($this->get30DayTrendData(Inmate::class, fn($q) => $q->where('is_discharged', false)))
                ->description("Total number of inmates")
                ->color('success'),
            Stat::make(
                'Convicts',
                number_format(\App\Models\Inmate::where('is_discharged', false)->count())
            )->chart($this->get30DayTrendData(Inmate::class, fn($q) => $q->where('is_discharged', false)))
                ->description('Total number of convicts currently in custody')
                ->icon('heroicon-o-user-group')
                ->color('primary'),
            Stat::make(
                'Remands',
                number_format(\App\Models\RemandTrial::where('detention_type', 'remand')
                    ->where('is_discharged', false)->count())
            )
                ->chart($this->get30DayTrendData(RemandTrial::class, fn($q) => $q->where('detention_type', 'remand')->where('is_discharged', false)))
                ->description("Total inmates on remand"),
            Stat::make(
                'Trial',
                number_format(\App\Models\RemandTrial::where('detention_type', 'trial')->where('is_discharged', false)->count())
            )
                ->chart($this->get30DayTrendData(RemandTrial::class, fn($q) => $q->where('detention_type', 'trial')->where('is_discharged', false)))
                ->description("Total inmates on trial"),
            Stat::make(
                'Expired Warrants',
                number_format(
                    \App\Models\RemandTrial::where('detention_type', 'remand')
                        ->whereDate('next_court_date', '<', now())
                        ->count()
                )
            )
                ->color('danger')
                ->chart($this->get30DayTrendData(
                    RemandTrial::class,
                    fn($q) => $q
                        ->where('detention_type', 'remand')
                        ->where('is_discharged', false)
                        ->whereDate('next_court_date', '<', now())
                ))
                ->description("Inmates on remand with expired warrants"),
            Stat::make(
                'Escapees',
                number_format(\App\Models\DischargedInmates::where('mode_of_discharge', 'escape')->count())
            )
                ->color('warning')

                ->description("Escaped inmates"),

            Stat::make(
                'Discharged Today',
                number_format(
                    \App\Models\DischargedInmates::whereDate('created_at', now())->count() //use discharged_date
                )
            )
                ->color('info')
                ->description("Inmates discharged today"),
            Stat::make(
                'Admissions Today',
                number_format(
                    \App\Models\Inmate::whereDate('created_at', now())->count() +
                        \App\Models\RemandTrial::whereDate('created_at', now())->count()
                )
            )
                ->color('info')
                ->description("Inmates admitted today"),

        ];
    }
}
