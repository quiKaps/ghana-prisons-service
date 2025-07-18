<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait Has30DayTrend
{
    public function get30DayTrendData(string $modelClass, ?\Closure $queryCallback = null): array
    {
        $query = $modelClass::query()
            ->whereDate('created_at', '>=', now()->subDays(29));

        if ($queryCallback) {
            $queryCallback($query);
        }

        $rawCounts = $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        return collect(range(0, 29))
            ->map(function ($i) use ($rawCounts) {
                $date = Carbon::today()->subDays(29 - $i)->toDateString();
                return $rawCounts[$date] ?? 0;
            })
            ->toArray();
    }
}
