<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrenchInterfaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_interface_uses_french_and_french_validation_messages(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<html lang="fr">', false);

        $response = $this->from(route('register'))->post(route('register'), []);

        $response->assertSessionHas('errors', fn ($errors) => $errors->first('first_name') === 'Le champ prénom est obligatoire.');
    }

    public function test_image_inputs_advertise_only_supported_image_formats(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('shops.create'))
            ->assertOk()
            ->assertSee('accept="image/jpeg,image/png,image/webp"', false);

        Shop::create([
            'user_id' => $user->id,
            'name' => 'Boutique de test',
            'slug' => 'boutique-de-test',
            'phone' => '+221770000000',
        ]);

        $this->actingAs($user)
            ->get(route('products.create'))
            ->assertOk()
            ->assertSee('accept="image/jpeg,image/png,image/webp"', false);
    }
}
