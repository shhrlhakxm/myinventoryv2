<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="My Inventory is a Laravel portfolio project for managing cafe items, stock movements, and low-stock visibility.">

    <title>{{ config('app.name', 'My Inventory') }} - Cafe Inventory Management</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white/95 backdrop-blur">
            <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
                aria-label="Primary navigation">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                    <x-application-logo class="h-9 w-9 fill-current text-indigo-600" />
                    <span class="text-lg font-semibold tracking-tight text-slate-900">My Inventory</span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            </nav>
        </header>

        <main>
            <section class="overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-sky-50">
                <div class="mx-auto grid max-w-7xl gap-14 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1 text-xs font-semibold text-indigo-700 shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            Laravel portfolio project
                        </div>

                        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">
                            Clear stock control for a busy cafe.
                        </h1>

                        <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">
                            Manage categories and items, record stock in, stock out, and adjustments, and review a complete inventory transaction history.
                        </p>

                        <div class="mt-7 flex flex-wrap gap-2" aria-label="Technology stack">
                            @foreach (['Laravel 13', 'PHP 8.4', 'Blade', 'Tailwind CSS 3', 'MySQL', 'PHPUnit'] as $technology)
                                <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-slate-600 shadow-sm">
                                    {{ $technology }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-9 flex flex-wrap items-center gap-4">
                            <a href="{{ route('login') }}"
                                class="rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                Explore the demo
                            </a>
                            <a href="#demo-access"
                                class="rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                View credentials
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xl shadow-indigo-100/60">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Inventory overview</p>
                                <p class="text-xs text-slate-500">Example dashboard preview</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Up to date
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-3 gap-3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs font-medium text-slate-500">Total items</p>
                                <p class="mt-1 text-xl font-bold text-slate-900">24</p>
                            </div>
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                                <p class="text-xs font-medium text-emerald-700">Healthy stock</p>
                                <p class="mt-1 text-xl font-bold text-emerald-800">21</p>
                            </div>
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                                <p class="text-xs font-medium text-amber-700">Low stock</p>
                                <p class="mt-1 text-xl font-bold text-amber-800">3</p>
                            </div>
                        </div>

                        <div class="mt-5 overflow-hidden rounded-xl border border-slate-200">
                            <div class="grid grid-cols-12 bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <span class="col-span-5">Item</span>
                                <span class="col-span-3">Category</span>
                                <span class="col-span-2 text-right">Stock</span>
                                <span class="col-span-2 text-right">Status</span>
                            </div>

                            <div class="divide-y divide-slate-200 text-sm">
                                <div class="grid grid-cols-12 items-center px-4 py-3">
                                    <span class="col-span-5 font-medium text-slate-800">Arabica beans</span>
                                    <span class="col-span-3 text-slate-500">Beverages</span>
                                    <span class="col-span-2 text-right tabular-nums text-slate-700">42</span>
                                    <span class="col-span-2 text-right text-xs font-medium text-emerald-700">In stock</span>
                                </div>
                                <div class="grid grid-cols-12 items-center px-4 py-3">
                                    <span class="col-span-5 font-medium text-slate-800">Paper cups</span>
                                    <span class="col-span-3 text-slate-500">Packaging</span>
                                    <span class="col-span-2 text-right tabular-nums text-slate-700">3</span>
                                    <span class="col-span-2 text-right text-xs font-medium text-amber-700">Low</span>
                                </div>
                                <div class="grid grid-cols-12 items-center px-4 py-3">
                                    <span class="col-span-5 font-medium text-slate-800">Oat milk</span>
                                    <span class="col-span-3 text-slate-500">Beverages</span>
                                    <span class="col-span-2 text-right tabular-nums text-slate-700">18</span>
                                    <span class="col-span-2 text-right text-xs font-medium text-emerald-700">In stock</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-y border-slate-200 bg-white py-16" aria-labelledby="features-heading">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Core workflow</p>
                        <h2 id="features-heading" class="mt-2 text-3xl font-bold tracking-tight text-slate-950">
                            Small scope, complete fundamentals
                        </h2>
                        <p class="mt-3 text-slate-600">
                            Built to demonstrate readable Laravel conventions and practical inventory safeguards.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                            <div class="inline-flex rounded-lg bg-indigo-100 p-2.5 text-indigo-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 font-semibold text-slate-900">Auditable stock movements</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Every stock-in, stock-out, and adjustment records its user, quantity, notes, and timestamp.
                            </p>
                        </article>

                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                            <div class="inline-flex rounded-lg bg-amber-100 p-2.5 text-amber-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M5.07 19h13.86a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16a2 2 0 001.73 3z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 font-semibold text-slate-900">Low-stock visibility</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Minimum-stock thresholds surface low items on the dashboard and item list without hiding the current quantity.
                            </p>
                        </article>

                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                            <div class="inline-flex rounded-lg bg-emerald-100 p-2.5 text-emerald-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.6-4A12 12 0 0112 3a12 12 0 01-8.6 3A12 12 0 003 9c0 5.6 3.8 10.3 9 11.6 5.2-1.3 9-6 9-11.6a12 12 0 00-.4-3z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 font-semibold text-slate-900">Role-based access</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Staff handle inventory work while administrators manage categories and user access through protected routes.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="demo-access" class="py-16">
                <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                    <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-6 sm:p-8">
                        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Demo access</p>
                                <h2 class="mt-1 text-2xl font-bold text-slate-950">Try both permission levels</h2>
                                <p class="mt-2 max-w-md text-sm text-slate-600">
                                    Seed the database, then use either account to explore the application.
                                </p>
                            </div>

                            <div class="grid gap-3 text-sm sm:grid-cols-2">
                                <div class="rounded-xl border border-indigo-200 bg-white p-4 shadow-sm">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Superadmin</p>
                                    <p class="mt-2 font-medium text-slate-900">admin@test.com</p>
                                    <p class="text-slate-500">Password: admin123</p>
                                </div>
                                <div class="rounded-xl border border-indigo-200 bg-white p-4 shadow-sm">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Staff</p>
                                    <p class="mt-2 font-medium text-slate-900">user@test.com</p>
                                    <p class="text-slate-500">Password: user123</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white py-6">
            <p class="text-center text-sm text-slate-500">
                My Inventory &copy; {{ date('Y') }}. Built with Laravel and Tailwind CSS.
            </p>
        </footer>
    </div>
</body>
</html>
