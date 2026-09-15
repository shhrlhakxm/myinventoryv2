<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Inventory Transactions
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
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
                            @forelse ($transactions as $transaction)
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $transaction->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                        {{ $transaction->item->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                                            {{ $transaction->type->label() }}
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-right font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format($transaction->quantity) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-600 dark:text-gray-300">
                                        {{ $transaction->user->name }}
                                    </td>
                                    <td class="min-w-[12rem] px-6 py-4 text-gray-600 dark:text-gray-300">
                                        {{ $transaction->notes ?: '—' }}
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
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
