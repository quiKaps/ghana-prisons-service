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

            // 📂 Custody Overview
            Stat::make(
                'Total locked up',
                number_format(
                    \App\Models\Inmate::active()->count() +
                        \App\Models\RemandTrial::where('is_discharged', false)->count()
                )
            )
                ->description("Total number of inmates across all categories")
                ->icon('heroicon-o-lock-closed')
                ->color('success')
                ->chart($this->get30DayTrendData(\App\Models\Inmate::class, fn($q) => $q->where('is_discharged', false)))
                ->chartColor('green'),

            Stat::make(
                'Convicts',
                number_format(\App\Models\Inmate::active()->count())
            )
                ->description('Convicted inmates currently in custody')
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->chart($this->get30DayTrendData(\App\Models\Inmate::class, fn($q) => $q->where('is_discharged', false)))
                ->chartColor('blue')
                ->extraAttributes([
                    'tooltip' => 'Includes only sentenced inmates not discharged or transferred out.',
                ]),

            Stat::make(
                'Remands',
                number_format(\App\Models\RemandTrial::remand()->count())
            )
                ->description("Inmates currently held on remand")
                ->icon('heroicon-o-scale')
                ->color('warning')
                ->chart($this->get30DayTrendData(
                    \App\Models\RemandTrial::class,
                    fn($q) =>
                    $q->where('detention_type', 'remand')->where('is_discharged', false)
                ))
                ->chartColor('amber'),

            Stat::make(
                'Trial',
                number_format(\App\Models\RemandTrial::trial()->count())
            )
                ->description("Inmates currently on trial")
                ->icon('heroicon-o-briefcase')
                ->color('info')
                ->chart($this->get30DayTrendData(
                    \App\Models\RemandTrial::class,
                    fn($q) =>
                    $q->where('detention_type', 'trial')->where('is_discharged', false)
                ))
                ->chartColor('cyan'),

            // 📂 Alerts & Exceptions
            Stat::make(
                'Expired Warrants',
                number_format(
                    \App\Models\RemandTrial::remand()
                        ->whereDate('next_court_date', '<', today())
                        ->count()
                )
            )
                ->description("Remand inmates with expired court warrants")
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->chart($this->get30DayTrendData(
                \App\Models\RemandTrial::class,
                fn($q) =>
                $q->where('detention_type', 'remand')
                        ->where('is_discharged', false)
                    ->whereDate('next_court_date', '<', today())
                ))
                ->chartColor('red'),

            Stat::make(
                'Escapees',
                number_format(
                    \App\Models\Inmate::where('is_discharged', true)
                        ->whereHas(
                            'discharge',
                            fn($q) =>
                            $q->where('discharge_type', 'escape')
                        )->count()
                        +
                        \App\Models\RemandTrial::where('is_discharged', true)
                        ->where('mode_of_discharge', 'escape')
                        ->count()
                )
            )
                ->description("Inmates who have escaped custody")
                ->icon('heroicon-o-flag')
                ->color('gray')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                    $q->whereHas(
                        'discharge',
                        fn($q) =>
                        $q->where('discharge_type', 'escape')
                    )
                ))
                ->chartColor('slate'),

            // 📂 Daily Activity
            Stat::make(
                'Discharged Today',
                number_format(
                    \App\Models\Inmate::whereHas(
                        'discharge',
                        fn($q) =>
                        $q->whereDate('discharge_date', today())
                    )->count()
                        +
                        \App\Models\RemandTrial::where('is_discharged', true)
                        ->whereDate('date_of_discharge', today())
                        ->count()
                )
            )
                ->description("Inmates discharged from custody today")
                ->icon('heroicon-o-arrow-up-right')
                ->color('teal')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                    $q->whereHas(
                        'discharge',
                        fn($q) =>
                        $q->whereDate('discharge_date', today())
                )
                ))
                ->chartColor('teal')
                ->extraAttributes([
                    'tooltip' => 'Includes both convicts and remand inmates discharged today.',
                ]),

            Stat::make(
                'Admissions Today',
                number_format(
                    \App\Models\Inmate::whereDate('created_at', today())->count()
                        +
                        \App\Models\RemandTrial::whereDate('created_at', today())->count()
                )
            )
                ->description("New inmate admissions today")
                ->icon('heroicon-o-plus-circle')
                ->color('emerald')
                ->chart($this->get30DayTrendData(
                    \App\Models\Inmate::class,
                    fn($q) =>
                    $q->whereDate('created_at', today())
                ))
                ->chartColor('emerald')
                ->extraAttributes([
                    'tooltip' => 'Includes all new admissions across remand and convict categories.',
                ]),
        ];
    }
}
