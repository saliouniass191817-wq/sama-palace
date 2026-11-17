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
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'phone' => 'not-a-phone',
            'address' => 'Dakar',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_normalizes_phone_format_before_storing_it(): void
    {
        $this->followingRedirects()->post(route('register'), [
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'phone' => '+221 (77) 000-00-00',
            'address' => 'Dakar',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertOk()
            ->assertSee('Veuillez remplir les informations de votre boutique');

        $this->assertDatabaseHas('users', ['phone' => '+221770000000']);
    }

    public function test_duplicate_phone_registration_uses_a_non_enumerating_message(): void
    {
        User::factory()->create(['phone' => '+221770000000']);

        $this->from(route('register'))->post(route('register'), [
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'phone' => '+221770000000',
            'address' => 'Dakar',
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

        $this->followingRedirects()->post(route('login'), [
            'phone' => '+221 (77) 000-00-00',
            'password' => 'secret-password',
        ])->assertOk()
            ->assertSeeText('Commencez à vendre en ligne');
    }
}
