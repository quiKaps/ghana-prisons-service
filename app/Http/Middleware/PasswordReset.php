<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        $this->isReset();


        return $next($request);
    }


    public function isReset()
    {
        return Action::make('verefiedPassword')
            ->modalHeading('Password Reset Prompt')
            ->modalSubmitActionLabel('Okay');
    }
}
