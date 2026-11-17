<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_shops_with_the_same_name_receive_unique_slugs(): void
    {
        foreach (range(1, 2) as $index) {
            $user = User::factory()->create();

            $this->actingAs($user)
                ->post(route('shops.store'), [
                    'name' => 'Nom',
                    'phone' => '+22177000000'.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                ])
                ->assertRedirect(route('dashboard'));
        }

        $this->assertDatabaseHas('shops', ['name' => 'Nom', 'slug' => 'nom']);
        $this->assertDatabaseHas('shops', ['name' => 'Nom', 'slug' => 'nom-2']);
    }
}
