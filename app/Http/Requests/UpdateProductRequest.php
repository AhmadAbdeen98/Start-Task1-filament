<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'cost_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ];
    }
}
