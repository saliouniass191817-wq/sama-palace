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
                'price' => '100000000.00',
            ])
            ->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_request_accepts_the_largest_decimal_column_value(): void
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
                'price' => '99999999.99',
            ])
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['price' => '99999999.99']);
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
