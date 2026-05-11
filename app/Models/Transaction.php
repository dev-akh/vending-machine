<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'unit_price',
        'quantity',
        'total_price',
        'amount_paid',
        'change_given',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:3',
        'total_price' => 'decimal:3',
        'amount_paid' => 'decimal:3',
        'change_given' => 'decimal:3',
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        return $this->save();
    }

    public function markAsFailed(): bool
    {
        $this->status = 'failed';
        return $this->save();
    }

    public function calculateChange(): float
    {
        return max(0, $this->amount_paid - $this->total_price);
    }
}
