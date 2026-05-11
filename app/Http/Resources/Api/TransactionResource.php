<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TransactionResource",
 *     title="Transaction Resource",
 *     description="Transaction resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Transaction ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="User ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="product_id",
 *         type="integer",
 *         description="Product ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="unit_price",
 *         type="number",
 *         format="decimal",
 *         description="Unit price at time of purchase",
 *         example=3.990
 *     ),
 *     @OA\Property(
 *         property="quantity",
 *         type="integer",
 *         description="Quantity purchased",
 *         example=2
 *     ),
 *     @OA\Property(
 *         property="total_price",
 *         type="number",
 *         format="decimal",
 *         description="Total price",
 *         example=7.980
 *     ),
 *     @OA\Property(
 *         property="amount_paid",
 *         type="number",
 *         format="decimal",
 *         description="Amount paid by customer",
 *         example=10.000
 *     ),
 *     @OA\Property(
 *         property="change_given",
 *         type="number",
 *         format="decimal",
 *         description="Change given to customer",
 *         example=2.020
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"completed", "pending", "failed"},
 *         description="Transaction status",
 *         example="completed"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'unit_price' => (float) number_format($this->unit_price, 3, '.', ''),
            'quantity' => $this->quantity,
            'total_price' => (float) number_format($this->total_price, 3, '.', ''),
            'amount_paid' => (float) number_format($this->amount_paid, 3, '.', ''),
            'change_given' => (float) number_format($this->change_given, 3, '.', ''),
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
