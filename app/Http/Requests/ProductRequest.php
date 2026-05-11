<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId),
            ],
            'price' => [
                'required',
                'numeric',
                'min:0.001',
                'max:99999.999',
                'regex:/^\d{1,5}(\.\d{1,3})?$/',
            ],
            'quantity_available' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'name.unique' => 'A product with this name already exists.',
            
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.001.',
            'price.max' => 'Price cannot exceed 99999.999.',
            'price.regex' => 'Price must be a valid monetary value with up to 3 decimal places.',
            
            'quantity_available.required' => 'Available quantity is required.',
            'quantity_available.integer' => 'Available quantity must be an integer.',
            'quantity_available.min' => 'Available quantity cannot be negative.',
            'quantity_available.max' => 'Available quantity cannot exceed 999,999.',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        
        if (isset($validated['price'])) {
            $validated['price'] = round((float) $validated['price'], 3);
        }
        
        return $validated;
    }
}
