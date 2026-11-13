<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_shop_displays_only_the_first_twelve_products(): void
    {
        $shop = $this->createShopWithProducts('Catalog item');

        $this->get(route('shops.public', $shop->slug))
            ->assertOk()
            ->assertSeeText('Catalog item 13')
            ->assertDontSeeText('Catalog item 01')
            ->assertSee('?page=2');
    }

    public function test_seller_dashboard_paginates_products_but_keeps_the_total_count(): void
    {
        $shop = $this->createShopWithProducts('Dashboard item');

        $this->actingAs($shop->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('13')
            ->assertSeeText('Dashboard item 13')
            ->assertDontSeeText('Dashboard item 01')
            ->assertSee('?page=2');
    }

    private function createShopWithProducts(string $prefix): Shop
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Pagination Test Shop',
            'slug' => 'pagination-test-shop',
            'phone' => '+19995550123',
        ]);

        foreach (range(1, 13) as $number) {
            $shop->products()->create([
                'name' => sprintf('%s %02d', $prefix, $number),
                'price' => 1000 + $number,
            ]);
        }

        return $shop;
    }
}
