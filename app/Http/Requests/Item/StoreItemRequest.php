<?php
// app/Http/Requests/Item/StoreItemRequest.php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:items,sku'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}