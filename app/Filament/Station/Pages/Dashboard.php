<?php

namespace App\Filament\Station\Pages;

use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $title = null;

    public function getTitle(): string
    {
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 18) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }
        $name = Auth::check() ? Auth::user()->name : 'Guest';
        return "{$greeting}, {$name}";
    }
}
