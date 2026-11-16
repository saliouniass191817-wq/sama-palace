<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_the_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'phone' => '+221770000000',
            'password' => 'secret-password',
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        $this->followingRedirects()->post(route('login'), [
            'phone' => '+221770000000',
            'password' => 'secret-password',
        ])->assertOk()
            ->assertSeeText('Tableau de bord administrateur')
            ->assertSee('href="'.route('admin.dashboard').'"', false);
    }

    public function test_only_admins_can_open_the_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $seller = User::factory()->create();

        $this->actingAs($seller)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Tableau de bord administrateur');
    }

    public function test_the_last_administrator_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();
    }

    public function test_sellers_cannot_open_admin_resource_lists(): void
    {
        $seller = User::factory()->create();

        foreach (['admin.users.index', 'admin.shops.index', 'admin.products.index'] as $routeName) {
            $this->actingAs($seller)
                ->get(route($routeName))
                ->assertForbidden();
        }
    }
}
