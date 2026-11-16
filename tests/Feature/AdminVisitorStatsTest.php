<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVisitorStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_unique_daily_visitors_for_the_site_and_each_shop(): void
    {
        $owner = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $owner->id,
            'name' => 'Visitor Shop',
            'slug' => 'visitor-shop',
            'phone' => '+221770000004',
        ]);

        $this->recordVisit('203.0.113.10', route('home'));
        $this->recordVisit('203.0.113.10', route('shops.public', $shop->slug));
        $this->recordVisit('203.0.113.10', route('home'));
        $this->recordVisit('203.0.113.11', route('shops.public', $shop->slug));

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Visiteurs uniques estimés aujourd’hui')
            ->assertSee('aria-label="2 visiteurs uniques estimés"', false)
            ->assertSeeText('Visitor Shop')
            ->assertSee('aria-label="2 visiteurs"', false);
    }

    public function test_a_repeat_visit_is_counted_again_on_a_new_day(): void
    {
        $this->travelTo(now()->startOfDay());
        $this->recordVisit('203.0.113.12', route('home'));
        $this->travel(1)->days();
        $this->recordVisit('203.0.113.12', route('home'));

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('aria-label="1 visiteur unique estimé"', false);
    }

    public function test_automated_and_authenticated_requests_are_not_counted_as_visitors(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.13'])
            ->withHeader('User-Agent', 'ExampleBot/1.0')
            ->get(route('home'))
            ->assertOk();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.13'])
            ->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk();

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('aria-label="0 visiteurs uniques estimés"', false);
    }

    private function recordVisit(string $ipAddress, string $url): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->withHeader('User-Agent', 'Mozilla/5.0')
            ->get($url)
            ->assertOk();
    }
}
