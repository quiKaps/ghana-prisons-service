<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PanelAccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            // Not authenticated, redirect to login
            return redirect()->route('login');
        }

        switch ($user->user_type) {
            case 'hq_admin':
                if (!$request->is('hq*')) {
                    return redirect('/hq');
                }
                break;
            case 'officer':
            case 'prison_admin':
                if (!$request->is('station*')) {
                    return redirect('/station');
                }
                break;
            default:
                // Optionally, deny access for unknown user types
                //abort(Response::HTTP_FORBIDDEN, 'Unauthorized access.');
                Filament::auth()->logout();
                session()->flash('status', 'Unauthorized access. Please contact support if you believe this is an error.');
                return redirect('/login');
        }

        return $next($request);
    }
}
