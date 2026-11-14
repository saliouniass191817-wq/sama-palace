<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClickPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_order_click_stores_only_a_salted_ip_hash(): void
    {
        $product = $this->createProduct();
        $ipAddress = '203.0.113.7';

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->get(route('orders.whatsapp', $product))
            ->assertRedirect();

        $storedIp = DB::table('clicks')->where('product_id', $product->id)->value('ip_address');

        $this->assertSame(
            substr(hash_hmac('sha256', $ipAddress, (string) config('app.key')), 0, 40),
            $storedIp,
        );
        $this->assertNotSame($ipAddress, $storedIp);
    }

    public function test_whatsapp_order_does_not_record_robot_or_prefetch_requests(): void
    {
        $product = $this->createProduct();

        $this->withHeader('User-Agent', 'ExampleBot/1.0')
            ->get(route('orders.whatsapp', $product))
            ->assertRedirect();

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0',
            'Purpose' => 'prefetch',
        ])
            ->get(route('orders.whatsapp', $product))
            ->assertRedirect();

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0',
            'Sec-Purpose' => 'prefetch;prerender',
        ])
            ->get(route('orders.whatsapp', $product))
            ->assertRedirect();

        $this->assertDatabaseCount('clicks', 0);
    }

    private function createProduct(): Product
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Privacy Test Shop',
            'slug' => 'privacy-test-shop',
            'phone' => '+19995550123',
        ]);

        return $shop->products()->create([
            'name' => 'Sample product',
            'price' => 12000,
        ]);
    }
}
