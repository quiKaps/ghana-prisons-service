<?php

namespace App\Providers;

use App\Models\Sentence;
use App\Observers\SentenceObserver;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sentence::observe(SentenceObserver::class);
    }
}
