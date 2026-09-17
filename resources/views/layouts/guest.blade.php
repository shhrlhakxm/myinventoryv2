<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'My Inventory') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-indigo-50 via-white to-sky-50 px-4 pt-8 sm:justify-center sm:pt-0">
            <div class="text-center">
                <a href="/" class="inline-flex items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    <x-application-logo class="h-12 w-12 fill-current text-indigo-600" />
                    <span class="text-xl font-semibold tracking-tight text-slate-900">My Inventory</span>
                </a>
            </div>

            <div class="mt-8 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-xl shadow-slate-200/60 sm:max-w-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
