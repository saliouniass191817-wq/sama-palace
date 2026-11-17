<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_product(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $owner = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $owner->id,
            'name' => 'Product Shop',
            'slug' => 'product-shop',
            'phone' => '+221770000003',
        ]);
        $this->actingAs($admin);

        $this->post(route('admin.products.store'), [
            'shop_id' => $shop->id,
            'name' => 'New Product',
            'price' => 1500,
            'description' => 'Description',
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'New Product')->firstOrFail();

        $this->put(route('admin.products.update', $product), [
            'shop_id' => $shop->id,
            'name' => 'Updated Product',
            'price' => 2500,
            'description' => 'Updated description',
        ])->assertRedirect(route('admin.products.index'));

        $this->get(route('admin.products.index'))
            ->assertOk()
            ->assertSeeText('Updated Product');

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->get(route('admin.products.index'))
            ->assertOk()
            ->assertDontSeeText('Updated Product');
    }

    public function test_admin_can_create_update_and_delete_a_shop(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $owner = User::factory()->create();
        $this->actingAs($admin);

        $this->post(route('admin.shops.store'), [
            'user_id' => $owner->id,
            'name' => 'Awa Boutique',
            'slug' => 'awa-boutique',
            'phone' => '+221770000002',
        ])->assertRedirect(route('admin.shops.index'));

        $shop = Shop::where('slug', 'awa-boutique')->firstOrFail();

        $this->put(route('admin.shops.update', $shop), [
            'user_id' => $owner->id,
            'name' => 'Awa Boutique Updated',
            'slug' => 'awa-boutique-updated',
            'phone' => '+221770000002',
        ])->assertRedirect(route('admin.shops.index'));

        $shop->refresh();

        $this->get(route('admin.shops.index'))
            ->assertOk()
            ->assertSeeText('Awa Boutique Updated');

        $this->delete(route('admin.shops.destroy', $shop))
            ->assertRedirect(route('admin.shops.index'));

        $this->get(route('admin.shops.index'))
            ->assertOk()
            ->assertDontSeeText('Awa Boutique Updated');
    }

    public function test_admin_can_create_update_and_delete_a_seller_account(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $this->actingAs($admin);

        $this->post(route('admin.users.store'), [
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'phone' => '+221770000001',
            'address' => 'Dakar',
            'password' => 'secret-password',
            'role' => 'admin',
        ])->assertRedirect(route('admin.users.index'));

        $seller = User::where('phone', '+221770000001')->firstOrFail();
        $this->assertFalse($seller->isAdmin());

        $this->put(route('admin.users.update', $seller), [
            'first_name' => 'Awa Updated',
            'last_name' => 'Diop',
            'phone' => '+221770000001',
            'address' => 'Dakar',
        ])->assertRedirect(route('admin.users.index'));

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeText('Awa Updated');

        $this->delete(route('admin.users.destroy', $seller))
            ->assertRedirect(route('admin.users.index'));

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertDontSeeText('Awa Updated');
    }

    public function test_deleting_a_seller_also_removes_their_shop_and_products(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $seller = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'Seller Shop',
            'slug' => 'seller-shop',
            'phone' => '+221770000007',
        ]);
        $shop->products()->create([
            'name' => 'Seller Product',
            'price' => 1000,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $seller))
            ->assertRedirect(route('admin.users.index'));

        $this->get(route('shops.public', $shop->slug))->assertNotFound();
        $this->get(route('admin.products.index'))->assertDontSeeText('Seller Product');
    }
}
