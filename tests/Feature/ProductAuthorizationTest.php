<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_seller_cannot_update_another_sellers_product(): void
    {
        $owner = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $owner->id,
            'name' => 'Owner Shop',
            'slug' => 'owner-shop',
            'phone' => '+221770000000',
        ]);
        $product = $shop->products()->create([
            'name' => 'Sac wax',
            'price' => 12000,
        ]);
        $otherSeller = User::factory()->create();

        $this->actingAs($otherSeller)
            ->get(route('products.create'))
            ->assertForbidden();

        $this->actingAs($otherSeller)
            ->get(route('products.edit', $product))
            ->assertForbidden();

        $this->actingAs($otherSeller)
            ->put(route('products.update', $product), [
                'name' => 'Unauthorized edit',
                'price' => 1,
            ])
            ->assertForbidden();

        $this->actingAs($otherSeller)
            ->delete(route('products.destroy', $product))
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Sac wax',
            'price' => 12000,
        ]);
    }
}
