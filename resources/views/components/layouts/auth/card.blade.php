<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body style="
    background-image: url('{{ asset('gpslogo.png') }}');
    background-repeat: repeat;
    background-position: center-left;
    background-size: 930px 930px;
    background-color: rgba(255,255,255,0.8); /* adjust transparency */
    background-blend-mode: overlay; /* or multiply, overlay, etc. */
"
 class="min-h-screen bg-neutral-100 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div  class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 bg-cover bg-center bg-no-repeat" >
            <h2 class="text-4xl font-bold">
                Ghana Prison Service
            </h2>
            <h3 class="text-2xl font-bold">Inmate Data Management System</h3>
            <div class="flex w-full max-w-md flex-col gap-6">
               
                <div class="flex flex-col gap-6">
                    <div class="bg-white rounded-xl border dark:bg-stone-950 dark:border-stone-800 text-stone-800 shadow-xs">
                        <div class="px-10 py-8">{{ $slot }}</div>
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
