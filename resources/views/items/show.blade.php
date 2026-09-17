<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-slate-800 ">
                Item Details
            </h2>

            <a href="{{ route('items.index') }}" class="text-sm text-indigo-600 hover:underline ">
                Back to Items
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm ">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-slate-900 ">
                        {{ $item->name }}
                    </h3>

                    <dl class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <dt class="text-sm text-slate-500 ">SKU</dt>
                            <dd class="mt-1 font-medium text-slate-900 ">
                                {{ $item->sku }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500 ">Category</dt>
                            <dd class="mt-1 font-medium text-slate-900 ">
                                {{ $item->category->name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500 ">Current Stock</dt>
                            <dd
                                class="mt-1 font-medium {{ $item->current_stock <= $item->minimum_stock ? 'text-amber-700' : 'text-slate-900' }}">
                                {{ number_format($item->current_stock) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500 ">Minimum Stock</dt>
                            <dd class="mt-1 font-medium text-slate-900 ">
                                {{ number_format($item->minimum_stock) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500 ">Unit Price</dt>
                            <dd class="mt-1 font-medium text-slate-900 ">
                                RM {{ number_format($item->unit_price, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>
            <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm ">
                <div class="border-b border-slate-200 px-6 py-4 ">
                    <h3 class="text-lg font-semibold text-slate-900 ">
                        Transaction History
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-600 ">
                            <tr>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Date</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Type</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3 text-right">Quantity</th>
                                <th scope="col" class="whitespace-nowrap px-6 py-3">Recorded By</th>
                                <th scope="col" class="px-6 py-3">Notes</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 ">
                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-slate-50 ">
                                    <td class="whitespace-nowrap px-6 py-4 text-slate-500 ">
                                        {{ $transaction->created_at->format('d M Y, H:i') }}
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
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 ">
                                        No transactions recorded for this item.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-6 py-4 ">
                    {{ $transactions->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
