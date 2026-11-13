<?php

namespace Tests\Feature;

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
}
