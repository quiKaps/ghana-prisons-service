<?php

namespace App\Filament\Station\Resources\ConvictDischargeResource\Pages;

use Filament\Actions;
use App\Models\Inmate;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Station\Resources\ConvictDischargeResource;

class ListConvictDischarges extends ListRecords
{
    protected static string $resource = ConvictDischargeResource::class;

    protected ?string $subheading = 'Manage and track discharged prisoners.';

    public function getTabs(): array
    {
        // $today = now()->toDateString();
        // $tomorrow = now()->addDay()->toDateString();


        // $counts = Inmate::whereIn('epd', [$today, $tomorrow])
        //     ->selectRaw('epd, COUNT(*) as count')
        //     ->groupBy('epd')
        //     ->pluck('count', 'epd');

        return [
            'today' => Tab::make('Today')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('is_discharged', true)
                        ->whereDoesntHave('discharge')
                        ->orderByDesc('created_at')
                )
                ->badge(Inmate::where('is_discharged', true)
                    ->whereDoesntHave('discharge')
                    ->orderByDesc('created_at')
                    ->count()),
            'tomorrow' => Tab::make('Tomorrow')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('is_discharged', true)
                        ->whereDoesntHave('discharge')
                        ->orderByDesc('created_at')
                )
                ->badge(Inmate::where('is_discharged', true)
                    ->whereDoesntHave('discharge')
                    ->orderByDesc('created_at')
                    ->count()),
            'thisMonth' => Tab::make('This Month')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('is_discharged', true)
                        ->whereHas('discharge', function ($q) {
                            $q->whereIn('discharge_type', [
                                'amnesty',
                                'fine_paid',
                                'presidential_pardon',
                                'acquitted_and_discharged',
                                'bail_bond',
                                'reduction_of_sentence',
                                'escape',
                                'death',
                                'one_third_remission'
                            ]);
                        })
                )
                ->badge(fn() => Inmate::where('is_discharged', true)
                    ->whereHas('discharge', function ($q) {
                        $q->whereIn('discharge_type', [
                            'amnesty',
                            'fine_paid',
                            'presidential_pardon',
                            'acquitted_and_discharged',
                            'bail_bond',
                            'reduction_of_sentence',
                            'escape',
                            'death',
                            'one_third_remission'
                        ]);
                    })->count()),
        ];
    }
}
