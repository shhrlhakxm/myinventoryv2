<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-4 p-6">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Total Items
                            </p>

                            <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($totalItems) }}
                            </p>
                        </div>

                        <div class="rounded-full bg-indigo-100 p-3 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 7 12 3 4 7m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-4 p-6">
                        <div class="flex flex-col gap-2">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Low Stock Items
                            </p>

                            <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ number_format($lowStockItems) }}
                            </p>
                        </div>

                        <div class="rounded-full bg-amber-100 p-3 text-amber-600 dark:bg-amber-900 dark:text-amber-300">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.38c.866-1.5 3.03-1.5 3.896 0l7.355 12.746ZM12 15.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <section class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800"
                aria-labelledby="recent-stock-movements-heading">
                <div class="flex flex-col gap-1 border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                    <h3 id="recent-stock-movements-heading"
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Recent Stock Movements
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        The five most recently recorded inventory updates.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-600 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Date</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Item</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Type</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3 text-right">Quantity</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Recorded By</th>
                                <th scope="col" class="px-6 py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentTransactions as $recentTransaction)
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $recentTransaction->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $recentTransaction->item->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                            {{ $recentTransaction->type->label() }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format($recentTransaction->quantity) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-600 dark:text-gray-300">
                                        {{ $recentTransaction->user->name }}
                                    </td>
                                    <td class="min-w-[12rem] px-6 py-4 text-gray-600 dark:text-gray-300">
                                        {{ $recentTransaction->notes ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No stock movements recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
