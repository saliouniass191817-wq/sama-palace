<?php

namespace Tests\Feature;

use App\Models\Click;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_accumulates_updates_and_removes_product_quantities(): void
    {
        [$shop, $product] = $this->createShopWithProduct('cart-shop', 'Sac wax', 12000);

        $this->post(route('cart.products.store', $product))
            ->assertRedirect();
        $this->post(route('cart.products.store', $product))
            ->assertRedirect()
            ->assertSessionHas('whatsapp_cart.items.'.$product->id, 2);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSeeText('Sac wax')
            ->assertSee('value="2"', false);

        $this->patch(route('cart.products.update', $product), ['quantity' => 3])
            ->assertRedirect()
            ->assertSessionHas('whatsapp_cart.items.'.$product->id, 3);

        $this->delete(route('cart.products.destroy', $product))
            ->assertRedirect()
            ->assertSessionMissing('whatsapp_cart');
    }

    public function test_cart_rejects_products_from_another_shop(): void
    {
        [, $firstProduct] = $this->createShopWithProduct('first-shop', 'Premier produit', 1000);
        [, $secondProduct] = $this->createShopWithProduct('second-shop', 'Autre produit', 2000);

        $this->post(route('cart.products.store', $firstProduct))
            ->assertRedirect();

        $this->from(route('shops.public', 'second-shop'))
            ->post(route('cart.products.store', $secondProduct))
            ->assertRedirect()
            ->assertSessionHasErrors('cart')
            ->assertSessionHas('whatsapp_cart.items.'.$firstProduct->id, 1)
            ->assertSessionMissing('whatsapp_cart.items.'.$secondProduct->id);
    }

    public function test_checkout_builds_one_whatsapp_message_from_current_products_and_records_clicks(): void
    {
        [$shop, $firstProduct] = $this->createShopWithProduct('checkout-shop', 'Sac wax', 12000);
        $secondProduct = $shop->products()->create([
            'name' => 'Foulard',
            'price' => 3500,
        ]);

        $this->post(route('cart.products.store', $firstProduct));
        $this->post(route('cart.products.store', $firstProduct));
        $this->post(route('cart.products.store', $secondProduct));

        $response = $this->post(route('cart.checkout'))->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertIsString($location);
        $this->assertStringStartsWith('https://wa.me/221770000000?text=', $location);

        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
        $message = $query['text'] ?? '';
        $this->assertStringContainsString('Sac wax x 2', $message);
        $this->assertStringContainsString('Foulard x 1', $message);
        $this->assertStringContainsString('Total : 27 500 FCFA', $message);

        $this->assertDatabaseCount('clicks', 2);
        $this->assertDatabaseHas('clicks', [
            'product_id' => $firstProduct->id,
            'shop_id' => $shop->id,
            'ip_address' => null,
        ]);
        $this->assertDatabaseHas('clicks', [
            'product_id' => $secondProduct->id,
            'shop_id' => $shop->id,
            'ip_address' => null,
        ]);
        $this->assertSame(2, session('whatsapp_cart.items.'.$firstProduct->id));
    }

    public function test_dashboard_hides_whatsapp_click_counts_but_keeps_product_count(): void
    {
        [$shop, $product] = $this->createShopWithProduct('metrics-shop', 'Produit populaire', 1000);
        Click::create([
            'product_id' => $product->id,
            'shop_id' => $shop->id,
        ]);

        $this->actingAs($shop->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Produits')
            ->assertDontSeeText('Clics sur WhatsApp')
            ->assertDontSeeText('Produit le plus cliqué')
            ->assertDontSeeText('Cliques whatsapp');
    }

    public function test_cart_navigation_is_visible_to_visitors_but_hidden_from_sellers(): void
    {
        [$shop] = $this->createShopWithProduct('navigation-shop', 'Produit', 1000);
        $cartUrl = route('cart.index');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('href="'.$cartUrl.'"', false)
            ->assertSeeText('Panier');

        $this->actingAs($shop->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('href="'.$cartUrl.'"', false);
    }

    /**
     * @return array{Shop, Product}
     */
    private function createShopWithProduct(string $slug, string $productName, int $price): array
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Cart Test Shop',
            'slug' => $slug,
            'phone' => '+221770000000',
        ]);
        $product = $shop->products()->create([
            'name' => $productName,
            'price' => $price,
        ]);

        return [$shop, $product];
    }
}
