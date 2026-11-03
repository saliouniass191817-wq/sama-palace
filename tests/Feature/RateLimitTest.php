<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rejects_attempts_after_five_requests_in_one_minute(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login'), [
                'phone' => '+221770000000',
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post(route('login'), [
            'phone' => '+221770000000',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_whatsapp_order_clicks_are_throttled_and_not_logged_after_the_limit(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Demo Shop',
            'slug' => 'demo-shop',
            'phone' => '+221770000000',
        ]);
        $product = $shop->products()->create([
            'name' => 'Sac wax',
            'price' => 12000,
        ]);

        for ($click = 1; $click <= 30; $click++) {
            $this->get(route('orders.whatsapp', $product))->assertRedirect();
        }

        $this->get(route('orders.whatsapp', $product))->assertTooManyRequests();
        $this->assertSame(30, $product->clicks()->count());
    }
}
