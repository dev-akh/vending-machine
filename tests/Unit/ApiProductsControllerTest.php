<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ProductsController;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;

class ApiProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_returns_paginated_products()
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'data', 'meta']);
    }

    /** @test */
    public function admin_can_store_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Passport::actingAs($admin);

        $response = $this->postJson('/api/v1/products', [
            'name' => 'New Soda',
            'description' => 'Bubbles',
            'price' => 1.500,
            'quantity_available' => 10
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'New Soda']);
    }

    /** @test */
    public function unauthorized_user_cannot_delete_product()
    {
        $user = User::factory()->create(['role' => 'user']);
        $product = Product::factory()->create();

        Passport::actingAs($user);

        $response = $this->deleteJson("/api/v1/products/{$product->id}");
        $response->assertStatus(403);
    }
}
