{{-- resources/views/categories/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Categories
            </h2>
            <a href="{{ route('categories.create') }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                + Add Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

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
                            <th class="px-6 py-3">Code</th>
                            <th class="px-6 py-3">Items</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="border-t border-slate-200 transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-900 ">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-slate-900 ">{{ $category->code }}</td>
                                <td class="px-6 py-4 text-slate-900 ">{{ $category->items_count }}</td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('categories.edit', $category) }}"
                                        class="text-indigo-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                        class="inline"
                                        onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500 ">
                                    No categories yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
