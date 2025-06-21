<?php

namespace App\Filament\Station\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total locked up',
                number_format(\App\Models\Inmate::count() + \App\Models\RemandTrial::count())
            )
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->description("Total number of inmates")
                ->color('success'),
            Stat::make(
                'Convicts',
                number_format(\App\Models\Inmate::count())
            )
                ->description('Total number of convicts'),
            Stat::make(
                'Remands',
                number_format(\App\Models\RemandTrial::where('detention_type', 'remand')->count())
            )
                ->description("Total inmates on remand"),
            Stat::make(
                'Trial',
                number_format(\App\Models\RemandTrial::where('detention_type', 'trial')->count())
            )
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
