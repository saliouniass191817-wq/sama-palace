<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClickPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_order_click_does_not_persist_the_request_ip(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Privacy Test Shop',
            'slug' => 'privacy-test-shop',
            'phone' => '+19995550123',
        ]);
        $product = $shop->products()->create([
            'name' => 'Sample product',
            'price' => 12000,
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.7'])
            ->get(route('orders.whatsapp', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('clicks', [
            'product_id' => $product->id,
            'ip_address' => null,
        ]);
    }
}
