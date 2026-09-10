<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $transactionType->label() }} — {{ $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                    Current stock: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $item->current_stock }}</span>
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
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('items.index') }}" class="text-sm text-gray-600 dark:text-gray-400 mr-4">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>