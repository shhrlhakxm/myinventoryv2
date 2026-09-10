<?php
// app/Http/Requests/Item/UpdateItemRequest.php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
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
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('items', 'sku')->ignore($this->route('item')),
            ],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            // sengaja TAKDE current_stock di sini
        ];
    }
}