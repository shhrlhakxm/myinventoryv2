<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Inventory Transactions
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm ">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-600 ">
                            <tr>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Date</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Item</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Type</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3 text-right">Quantity</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Recorded By</th>
                                <th scope="col" class="px-6 py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 ">
                            @forelse ($transactions as $transaction)
                                <tr class="transition-colors hover:bg-slate-50 ">
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-500 ">
                                        {{ $transaction->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <a href="{{ route('items.show', $transaction->item) }}"
                                            class="font-medium text-indigo-600 hover:underline ">
                                            {{ $transaction->item->name }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700 ">
                                            {{ $transaction->type->label() }}
                                        </span>
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-right font-medium text-slate-900 ">
                                        {{ number_format($transaction->quantity) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-600 ">
                                        {{ $transaction->user->name }}
                                    </td>
                                    <td class="min-w-[12rem] px-6 py-4 text-slate-600 ">
                                        {{ $transaction->notes ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 ">
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
