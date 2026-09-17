<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ $transactionType->label() }} — {{ $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                @if (session('error'))
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-800">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6 text-sm text-slate-600 ">
                    Current stock: <span class="font-semibold text-slate-900 ">{{ $item->current_stock }}</span>
                </div>

                <form method="POST" action="{{ route('stock-movements.store', $item) }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $transactionType->value }}">

                    <div>
                        <x-input-label for="quantity"
                            :value="$transactionType === \App\Enums\TransactionType::Adjustment ? 'Adjustment (+/-)' : 'Quantity'" />
                        <x-text-input id="quantity" name="quantity" type="number"
                            class="mt-1 block w-full" :value="old('quantity')" required autofocus />
                        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="notes"
                            :value="$transactionType === \App\Enums\TransactionType::Adjustment ? 'Reason (required)' : 'Notes'" />
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('items.index') }}" class="text-sm text-slate-600 mr-4">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
