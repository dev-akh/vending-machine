<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity_available',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'quantity_available' => 'integer',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isAvailable(int $quantity = 1): bool
    {
        return $this->quantity_available >= $quantity;
    }

    public function decreaseQuantity(int $quantity): bool
    {
        if (!$this->isAvailable($quantity)) {
            return false;
        }

        $this->quantity_available -= $quantity;
        return $this->save();
    }

    public function increaseQuantity(int $quantity): bool
    {
        $this->quantity_available += $quantity;
        return $this->save();
    }

    public function scopeAvailable($query)
    {
        return $query->where('quantity_available', '>', 0);
    }

    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderBy('name', $direction);
    }
}
