<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900" style="background-color:#F7F1F0">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800" style="background-image: url('{{ asset('gpsbg.webp') }}'); background-repeat: no-repeat; background-size: cover; background-position: center;">
            </div>
            <div class="w-full lg:p-8">
                <img  class="h-24 mx-auto mb-7 md:visible" src="{{ asset('gps-logo.png') }}" alt="gps logo"/>
                <div class="mx-auto flex md:shadow-lg bg w-full flex-col justify-center space-y-6 md:w-[50%] md:outline-3 rounded-sm border-solid border-ambers-900 md:p-10 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="sr-only">{{ config('app.name', 'GPS Portal') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
