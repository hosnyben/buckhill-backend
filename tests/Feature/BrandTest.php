<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use App\Models\Brand;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    protected $credentials = ['guest@buckhill.co.uk','password'];

    protected function createUser($admin): User
    {
        return User::factory()->create([
            'email' => $this->credentials[0],
            'password' => $this->credentials[1],
            'is_admin' => $admin
        ]);
    }

    public function test_listing_brands(): void
    {
        $this->seed();

        $response = $this->getJson(route('brand.index'));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'slug',
                ],
            ],
            'current_page',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    }

    public function test_show_brand(): void
    {
        $this->seed();

        $brand = Brand::inRandomOrder()->first();

        $response = $this->getJson(route('brand.show', ['brand' => $brand->slug]));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'title',
                'slug',
            ],
            'error',
            'errors',
            'extra'
        ]);
    }

    public function test_show_brand_with_wrong_slug(): void
    {
        $this->seed();

        $brand = Brand::inRandomOrder()->first();

        $response = $this->getJson(route('brand.show', ['brand' => $brand->slug.'-wrong']));

        $response->assertStatus(404);
    }

    public function test_create_brand(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        $response = $this->withToken($token)->postJson(route('brand.create'), [
            'title' => 'Test Brand',
            'slug' => 'test-brand',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'title',
                'slug',
            ],
            'error',
            'errors',
            'extra'
        ]);
    }

    public function test_create_brand_with_non_admin_user(): void
    {
        $user = $this->createUser(false);
        $token = $user->createToken('registerToken');

        $response = $this->withToken($token)->postJson(route('brand.create'), [
            'title' => 'Test Brand',
            'slug' => 'test-brand',
        ]);

        $response->assertStatus(403);
    }

    public function test_create_brand_without_auth(): void
    {
        $response = $this->postJson(route('brand.create'), [
            'title' => 'Test Brand',
            'slug' => 'test-brand',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_brand(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        $brand = Brand::factory()->create();

        $response = $this->withToken($token)->putJson(route('brand.update', ['brand' => $brand->slug]), [
            'title' => 'Test Brand Updated',
            'slug' => 'test-brand-updated',
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'title',
                'slug',
            ],
            'error',
            'errors',
            'extra'
        ]);
    }

    public function test_update_brand_with_non_admin_user(): void
    {
        $user = $this->createUser(false);
        $token = $user->createToken('registerToken');

        $brand = Brand::factory()->create();

        $response = $this->withToken($token)->putJson(route('brand.update', ['brand' => $brand->slug]), [
            'title' => 'Test Brand Updated',
            'slug' => 'test-brand-updated',
        ]);

        $response->assertStatus(403);
    }

    public function test_update_brand_without_auth(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->putJson(route('brand.update', ['brand' => $brand->slug]), [
            'title' => 'Test Brand Updated',
            'slug' => 'test-brand-updated',
        ]);

        $response->assertStatus(401);
    }

    public function test_delete_brand(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        $brand = Brand::factory()->create();

        $response = $this->withToken($token)->deleteJson(route('brand.delete', ['brand' => $brand->slug]));

        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra'
        ]);
    }

    public function test_delete_brand_with_non_admin_user(): void
    {
        $user = $this->createUser(false);
        $token = $user->createToken('registerToken');

        $brand = Brand::factory()->create();

        $response = $this->withToken($token)->deleteJson(route('brand.delete', ['brand' => $brand->slug]));

        $response->assertStatus(403);
    }

    public function test_delete_brand_without_auth(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->deleteJson(route('brand.delete', ['brand' => $brand->slug]));

        $response->assertStatus(401);
    }
}
