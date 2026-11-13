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

    public function test_product_edit_form_displays_integer_fcfa_prices_without_decimal_zeros(): void
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

        $this->actingAs($user)
            ->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('id="product-price" type="number" name="price" min="0" step="1" value="12000"', false);
    }

    public function test_product_request_rejects_fractional_fcfa_prices(): void
    {
        $user = User::factory()->create();
        Shop::create([
            'user_id' => $user->id,
            'name' => 'Demo Shop',
            'slug' => 'demo-shop',
            'phone' => '+19995550123',
        ]);

        $this->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Sample product',
                'price' => '1250.50',
            ])
            ->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_request_rejects_a_price_above_the_decimal_column_limit(): void
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
                'price' => '100000000',
            ])
            ->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_request_accepts_the_largest_fcfa_integer_price(): void
    {
        $user = User::factory()->create();
        Shop::create([
            'user_id' => $user->id,
            'name' => 'Demo Shop',
            'slug' => 'demo-shop',
            'phone' => '+19995550123',
        ]);

        $this->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Sac wax',
                'price' => '99999999',
            ])
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['price' => '99999999']);
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

    public function test_shop_request_accepts_international_e164_phone_numbers(): void
    {
        foreach (['+12345678', '+123456789012345'] as $index => $phone) {
            $user = User::factory()->create();

            $this->actingAs($user)
                ->post(route('shops.store'), [
                    'name' => 'Demo Shop '.$index,
                    'phone' => $phone,
                ])
                ->assertRedirect(route('dashboard'));

            $this->assertDatabaseHas('shops', ['phone' => $phone]);
        }
    }

    public function test_shop_request_rejects_unusable_international_phone_numbers(): void
    {
        foreach (['221770000000', '+012345678', '+1234567', '+1234567890123456', '+22177000000x'] as $index => $phone) {
            $user = User::factory()->create();

            $this->actingAs($user)
                ->post(route('shops.store'), [
                    'name' => 'Demo Shop '.$index,
                    'phone' => $phone,
                ])
                ->assertSessionHasErrors('phone');
        }

        $this->assertDatabaseCount('shops', 0);
    }
}
