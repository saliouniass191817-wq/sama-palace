<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceFormattingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_product_card_does_not_round_fractional_prices(): void
    {
        $shop = $this->createShopWithFractionalPrice();

        $this->get(route('shops.public', $shop->slug))
            ->assertOk()
            ->assertSeeText('1 250,50 FCFA');
    }

    public function test_whatsapp_order_message_does_not_round_fractional_prices(): void
    {
        $shop = $this->createShopWithFractionalPrice();
        $product = $shop->products()->firstOrFail();

        $response = $this->get(route('orders.whatsapp', $product));
        $response->assertRedirect();

        parse_str(parse_url($response->headers->get('Location'), PHP_URL_QUERY), $query);
        $this->assertStringContainsString('Price: 1 250,50 FCFA.', $query['text']);
    }

    private function createShopWithFractionalPrice(): Shop
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Price Test Shop',
            'slug' => 'price-test-shop',
            'phone' => '+19995550123',
        ]);
        $shop->products()->create([
            'name' => 'Fractional product',
            'price' => '1250.50',
        ]);

        return $shop;
    }
}
