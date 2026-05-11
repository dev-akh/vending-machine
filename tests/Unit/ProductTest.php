<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{

    public function test_product_can_be_created()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 5.000,
            'quantity_available' => 10
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(5.000, $product->price);
        $this->assertEquals(10, $product->quantity_available);
    }

    public function test_is_available_returns_correct_value()
    {
        $product = Product::factory()->create(['quantity_available' => 10]);

        $this->assertTrue($product->isAvailable(5));
        $this->assertTrue($product->isAvailable(10));
        $this->assertFalse($product->isAvailable(15));
    }

    public function test_decrease_quantity_works_correctly()
    {
        $product = Product::factory()->create(['quantity_available' => 10]);

        $this->assertTrue($product->decreaseQuantity(3));
        $this->assertEquals(7, $product->fresh()->quantity_available);

        $this->assertFalse($product->decreaseQuantity(10));
        $this->assertEquals(7, $product->fresh()->quantity_available);
    }

    public function test_increase_quantity_works_correctly()
    {
        $product = Product::factory()->create(['quantity_available' => 10]);

        $this->assertTrue($product->increaseQuantity(5));
        $this->assertEquals(15, $product->fresh()->quantity_available);
    }
}
