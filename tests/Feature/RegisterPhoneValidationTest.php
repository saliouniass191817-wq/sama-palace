<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterPhoneValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_rejects_a_phone_number_outside_e164_format(): void
    {
        $this->post(route('register'), [
            'firstName' => 'Awa',
            'lastName' => 'Diop',
            'phone' => 'not-a-phone',
            'adress' => 'Dakar',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_normalizes_phone_format_before_storing_it(): void
    {
        $this->post(route('register'), [
            'firstName' => 'Awa',
            'lastName' => 'Diop',
            'phone' => '+221 (77) 000-00-00',
            'adress' => 'Dakar',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('shops.create'));

        $this->assertDatabaseHas('users', ['phone' => '+221770000000']);
    }

    public function test_duplicate_phone_registration_uses_a_non_enumerating_message(): void
    {
        User::factory()->create(['phone' => '+221770000000']);

        $this->from(route('register'))->post(route('register'), [
            'firstName' => 'Awa',
            'lastName' => 'Diop',
            'phone' => '+221770000000',
            'adress' => 'Dakar',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertSessionHasErrors([
            'phone' => 'Impossible de créer un compte avec ces informations.',
        ]);
    }

    public function test_login_normalizes_phone_format_before_authentication(): void
    {
        User::factory()->create([
            'phone' => '+221770000000',
            'password' => 'secret-password',
        ]);

        $this->post(route('login'), [
            'phone' => '+221 (77) 000-00-00',
            'password' => 'secret-password',
        ])->assertRedirect(route('dashboard'));
    }
}
