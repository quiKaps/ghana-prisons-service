<?php

namespace App\Providers;

use App\Models\Sentence;
use App\Observers\SentenceObserver;
use App\Http\Responses\LogoutResponse;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sentence::observe(SentenceObserver::class);
    }
}
