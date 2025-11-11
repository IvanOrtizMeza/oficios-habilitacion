<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head') {{-- incluye el head --}}
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900" style="background:#dfdfd5">
       <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <img src="{{ asset('img/morelosimg.svg') }}" 
            alt="Gobierno del Estado de Morelos" style="height: 80px"
            >

            <div class="flex w-full max-w-md  flex-col gap-2 bg-white p-8 shadow-md sm:mt-20" style="border-radius: 20px; margin-top:30px">
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
