<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
})->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->type !== 'hq_admin') {
        return redirect('/station');
    } else {
        return redirect('/hq');
    }
})->middleware(['auth', 'verified'])
    ->name('dashboard');

// Add this route for viewing horizon dashbaord

Route::get('/horizon', function () {

    if (Auth::check() && Auth::user()?->type !== 'super_admin') {
        return redirect('/horizon');
    } else {
        return redirect('/dashboard');
    }
})->middleware(['auth'])
    ->name('horizon');

Route::get('/storage/{document}', function () {})
    ->name('warrant.document.view');


Route::get('/station/inmates/{id}/edit')
    ->middleware(['auth', 'verified', 'password.confirm']);

Route::middleware(['auth'])->group(function () {

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
