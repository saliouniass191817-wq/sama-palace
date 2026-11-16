<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_a_dedicated_admin_from_configured_credentials(): void
    {
        config([
            'admin.phone' => '+221770000005',
            'admin.password' => 'a-strong-admin-password',
            'admin.first_name' => 'Aminata',
            'admin.last_name' => 'Admin',
            'admin.address' => 'Dakar',
        ]);

        Artisan::call('db:seed', [
            '--class' => AdminUserSeeder::class,
        ]);

        $admin = User::where('phone', '+221770000005')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue(Hash::check('a-strong-admin-password', $admin->password));
    }

    public function test_admin_seeder_does_not_promote_an_existing_seller(): void
    {
        config([
            'admin.phone' => '+221770000006',
            'admin.password' => 'a-strong-admin-password',
        ]);
        User::factory()->create(['phone' => '+221770000006']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ADMIN_PHONE already belongs to a non-administrator account.');

        app(AdminUserSeeder::class)->run();
    }
}
