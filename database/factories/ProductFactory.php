<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->words(1, true)) . ' ' . fake()->randomElement(['Snack', 'Drink', 'Bar', 'Chips']),
            'price' => $this->faker->randomFloat(3, 0.5, 50),
            'quantity_available' => $this->faker->numberBetween(1, 50),
        ];
    }
}
