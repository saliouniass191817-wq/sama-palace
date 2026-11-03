<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_seller_cannot_change_another_sellers_shop(): void
    {
        $owner = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $owner->id,
            'name' => 'Owner Shop',
            'slug' => 'owner-shop',
            'phone' => '+221770000000',
        ]);
        $otherSeller = User::factory()->create();

        $this->actingAs($otherSeller)
            ->put(route('shops.update'), [
                'name' => 'Unauthorized edit',
                'phone' => '+221771111111',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Owner Shop',
            'phone' => '+221770000000',
        ]);
    }
}
