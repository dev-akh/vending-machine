<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return \Illuminate\Support\Facades\Auth::check();
    }

    public function rules(): array
    {
        $product = $this->route('product');
        
        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
                "max:{$product->quantity_available}",
            ],
            'amount_paid' => [
                'required',
                'numeric',
                'min:0.001',
                'regex:/^\d{1,5}(\.\d{1,3})?$/',
            ],
        ];
    }

    public function messages(): array
    {
        $product = $this->route('product');
        
        return [
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => "Cannot purchase more than {$product->quantity_available} units.",
            
            'amount_paid.required' => 'Amount paid is required.',
            'amount_paid.numeric' => 'Amount paid must be a number.',
            'amount_paid.min' => 'Amount paid must be at least 0.001.',
            'amount_paid.regex' => 'Amount paid must be a valid monetary value with up to 3 decimal places.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $product = $this->route('product');
            $quantity = $this->input('quantity');
            $amountPaid = $this->input('amount_paid');
            
            if ($quantity && $amountPaid) {
                $totalPrice = $product->price * $quantity;
                
                if ($amountPaid < $totalPrice) {
                    $validator->errors()->add('amount_paid', 
                        'Amount paid is insufficient. Total price: $' . number_format($totalPrice, 3));
                }
            }
        });
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        
        if (isset($validated['amount_paid'])) {
            $validated['amount_paid'] = round((float) $validated['amount_paid'], 3);
        }
        
        return $validated;
    }
}
