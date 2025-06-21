<?php

namespace App\Filament\Station\Pages;

use Illuminate\Support\Facades\Auth;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $title = null;

    public function getTitle(): string
    {
        return 'Welcome ' . (Auth::check() ? Auth::user()->name : 'Guest');
    }
}
