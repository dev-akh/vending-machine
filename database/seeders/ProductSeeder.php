<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            ['name' => 'Coke', 'price' => 3.990, 'quantity_available' => 15],
            ['name' => 'Pepsi', 'price' => 6.885, 'quantity_available' => 12],
            ['name' => 'Water', 'price' => 0.500, 'quantity_available' => 20],
            ['name' => 'Sprite', 'price' => 3.500, 'quantity_available' => 10],
            ['name' => 'Dr. Pepper', 'price' => 4.250, 'quantity_available' => 8],
            ['name' => 'Lays Classic', 'price' => 2.500, 'quantity_available' => 25],
            ['name' => 'Doritos Nacho', 'price' => 2.750, 'quantity_available' => 18],
            ['name' => 'Snickers Bar', 'price' => 1.990, 'quantity_available' => 30],
            ['name' => 'KitKat', 'price' => 1.850, 'quantity_available' => 22],
            ['name' => 'Red Bull', 'price' => 5.500, 'quantity_available' => 14],
        ]);

        \App\Models\Product::factory(20)->create();
    }
}
