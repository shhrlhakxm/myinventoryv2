{{-- resources/views/items/_form.blade.php --}}
@csrf

<div>
    <x-input-label for="category_id" value="Category" />
    <select id="category_id" name="category_id" required
        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">-- Select Category --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('category_id', $item->category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="name" value="Item Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
        :value="old('name', $item->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="sku" value="SKU" />
    <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full"
        :value="old('sku', $item->sku ?? '')" required />
    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
</div>

@unless (isset($item))
    <div class="mt-4">
        <x-input-label for="current_stock" value="Initial Stock" />
        <x-text-input id="current_stock" name="current_stock" type="number" min="0" class="mt-1 block w-full"
            :value="old('current_stock', 0)" required />
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Quantity currently on hand when adding this item to the system.
        </p>
        <x-input-error :messages="$errors->get('current_stock')" class="mt-2" />
    </div>
@endunless

<div class="mt-4">
    <x-input-label for="minimum_stock" value="Minimum Stock (Low Stock Alert)" />
    <x-text-input id="minimum_stock" name="minimum_stock" type="number" min="0" class="mt-1 block w-full"
        :value="old('minimum_stock', $item->minimum_stock ?? 5)" required />
    <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="unit_price" value="Unit Price (RM)" />
    <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full"
        :value="old('unit_price', $item->unit_price ?? '')" required />
    <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('items.index') }}" class="text-sm text-gray-600 dark:text-gray-400 mr-4">Cancel</a>
    <x-primary-button>{{ isset($item) ? 'Update' : 'Save' }}</x-primary-button>
</div>