<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="CreateProductRequest",
 *     title="Create Product Request",
 *     description="Request schema for creating a product",
 *     required={"name", "price", "quantity_available"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         maxLength=255,
 *         description="Product name",
 *         example="Coca Cola"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         maxLength=1000,
 *         description="Product description",
 *         example="Refreshing cola drink"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="decimal",
 *         minimum=0.001,
 *         maximum=99999.999,
 *         description="Product price",
 *         example=3.990
 *     ),
 *     @OA\Property(
 *         property="quantity_available",
 *         type="integer",
 *         minimum=0,
 *         maximum=999999,
 *         description="Available quantity",
 *         example=50
 *     )
 * )
 */
class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:products,name'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'price' => [
                'required',
                'numeric',
                'min:0.001',
                'max:99999.999'
            ],
            'quantity_available' => [
                'required',
                'integer',
                'min:0',
                'max:999999'
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
        return [
            'name.required' => 'Product name is required.',
            'name.unique' => 'A product with this name already exists.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be at least 0.001.',
            'price.max' => 'Price cannot exceed 99999.999.',
            'quantity_available.required' => 'Quantity is required.',
            'quantity_available.min' => 'Quantity cannot be negative.',
            'quantity_available.max' => 'Quantity cannot exceed 999999.',
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
