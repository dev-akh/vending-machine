<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Tests\TestCase;

class BasicApiTest extends TestCase
{
    public function test_api_login_endpoint_exists()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401);
    }

    public function test_api_products_endpoint_exists()
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\Product::factory()->create(['name' => 'Test Product']);

        // 2. Act as the user via Passport
        \Laravel\Passport\Passport::actingAs($user);

        // 3. Make the request
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data'
            ]);
    }

    public function test_api_me_endpoint_requires_auth()
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}
