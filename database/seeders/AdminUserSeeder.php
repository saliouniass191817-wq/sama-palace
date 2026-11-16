<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phone = config('admin.phone');
        $password = config('admin.password');

        if (! is_string($phone) || ! preg_match('/^\+[1-9][0-9]{7,14}$/', $phone)) {
            throw new RuntimeException('Set ADMIN_PHONE to a valid E.164 phone number before seeding the administrator.');
        }

        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_PASSWORD to a password with at least 12 characters before seeding the administrator.');
        }

        $user = User::where('phone', $phone)->first();

        if ($user) {
            if (! $user->isAdmin()) {
                throw new RuntimeException('ADMIN_PHONE already belongs to a non-administrator account. Use a dedicated administrator phone.');
            }

            return;
        }

        $user = User::create([
            'firstName' => config('admin.first_name'),
            'lastName' => config('admin.last_name'),
            'phone' => $phone,
            'adress' => config('admin.address'),
            'password' => $password,
        ]);
        $user->forceFill(['role' => 'admin'])->save();
    }
}
