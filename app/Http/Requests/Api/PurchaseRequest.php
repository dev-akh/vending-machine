<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="PurchaseRequest",
 *     title="Purchase Request",
 *     description="Request schema for product purchase",
 *     required={"quantity", "amount_paid"},
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         minimum=1,
 *         description="Quantity to purchase",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="amount_paid",
 *         type="number",
 *         format="decimal",
 *         minimum=0.001,
 *         description="Amount paid by customer",
 *         example=10.000
 *     )
 * )
 */
class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $product = $this->route('product');
        
        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:' . ($product ? $product->quantity_available : 0)
            ],
            'amount_paid' => [
                'required',
                'numeric',
                'min:0.001'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $product = $this->route('product');
        
        return [
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Cannot purchase more than ' . ($product ? $product->quantity_available : 0) . ' items.',
            'amount_paid.required' => 'Amount paid is required.',
            'amount_paid.min' => 'Amount paid must be at least 0.001.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
