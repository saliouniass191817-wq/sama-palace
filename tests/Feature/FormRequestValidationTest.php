<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_request_rejects_a_negative_price(): void
    {
        $user = User::factory()->create();
        Shop::create([
            'user_id' => $user->id,
            'name' => 'Demo Shop',
            'slug' => 'demo-shop',
            'phone' => '+221770000000',
        ]);

        $this->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Sac wax',
                'price' => -1,
            ])
            ->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_shop_request_requires_a_contact_phone(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('shops.store'), [
                'name' => 'Demo Shop',
            ])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('shops', 0);
    }
}
