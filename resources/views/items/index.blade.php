<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Items
            </h2>
            <a href="{{ route('items.create') }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                + Add Item
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">SKU</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Stock</th>
                            <th class="px-6 py-3">Unit Price</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr class="border-t border-slate-200 transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('items.show', $item) }}"
                                        class="font-medium text-indigo-600 hover:underline ">
                                        {{ $item->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-slate-900 ">{{ $item->sku }}</td>
                                <td class="px-6 py-4 text-slate-900 ">{{ $item->category->name }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="{{ $item->current_stock <= $item->minimum_stock ? 'font-semibold text-amber-700' : 'text-slate-900' }}">
                                        {{ $item->current_stock }}
                                    </span>
                                    @if ($item->current_stock <= $item->minimum_stock)
                                        <span class="text-xs font-medium text-amber-700">(Low)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-900 ">RM
                                    {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('stock-movements.create', [$item, 'in']) }}"
                                        class="text-emerald-600 hover:underline text-xs">In</a>
                                    <a href="{{ route('stock-movements.create', [$item, 'out']) }}"
                                        class="text-amber-600 hover:underline text-xs">Out</a>
                                    <a href="{{ route('stock-movements.create', [$item, 'adjustment']) }}"
                                        class="text-xs text-indigo-600 hover:underline">Adjust</a>
                                    <span class="text-slate-300 ">|</span>
                                    <a href="{{ route('items.edit', $item) }}"
                                        class="text-indigo-600 hover:underline text-xs">Edit</a>
                                    <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline"
                                        onsubmit="return confirm('Delete this item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-600 hover:underline text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 ">
                                    No items yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
