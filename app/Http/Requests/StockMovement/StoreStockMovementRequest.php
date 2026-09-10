<?php

namespace App\Http\Requests\StockMovement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', $type === 'adjustment' ? 'min:-999999' : 'min:1'],
            'notes' => [$type === 'adjustment' ? 'required' : 'nullable', 'string', 'max:500'],
        ];
    }
}
