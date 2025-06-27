<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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


Route::get('/station/inmates/{id}/edit')
    ->middleware(['auth', 'verified', 'password.confirm']);

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
