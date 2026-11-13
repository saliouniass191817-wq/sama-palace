<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_public_and_lists_all_shops_over_paginated_pages(): void
    {
        foreach (range(1, 13) as $number) {
            $user = User::factory()->create();
            Shop::create([
                'user_id' => $user->id,
                'name' => sprintf('Boutique %02d', $number),
                'slug' => sprintf('boutique-%02d', $number),
                'phone' => '+12345678',
            ]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Boutique 13')
            ->assertSee('?page=2');

        $this->get(route('home', ['page' => 2]))
            ->assertOk()
            ->assertSeeText('Boutique 01')
            ->assertDontSeeText('Boutique 13');
    }

    public function test_authenticated_seller_can_view_the_public_homepage_and_dashboard_link(): void
    {
        $user = User::factory()->create();
        Shop::create([
            'user_id' => $user->id,
            'name' => 'Ma boutique',
            'slug' => 'ma-boutique',
            'phone' => '+12345678',
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSeeText('Ma boutique')
            ->assertSee('href="'.route('dashboard').'"', false);
    }
}
