<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::user();

        if (!$user->is_active) {
            // User is not active, redirect to login with  prompt
            Filament::auth()->logout();
            session()->flash('status', 'You are not authorized to access this account. Please contact admin if you believe this is an error.');
            return redirect('/login');
        }



        return $next($request);
    }
}
