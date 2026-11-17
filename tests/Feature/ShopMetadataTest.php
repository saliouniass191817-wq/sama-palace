<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_shop_includes_dynamic_title_and_social_metadata(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Boutique Sénégal',
            'slug' => 'boutique-senegal',
            'description' => 'Des créations locales.',
            'phone' => '+221770000000',
        ]);
        $shop->products()->create([
            'name' => 'Sac artisanal',
            'price' => 12500,
        ]);

        $this->get(route('shops.public', $shop->slug))
            ->assertOk()
            ->assertSee('<title>'.$shop->name.' | '.config('app.name', 'Sama Palace').'</title>', false)
            ->assertSee('property="og:title" content="'.$shop->name.' | '.config('app.name', 'Sama Palace').'"', false)
            ->assertSee('property="og:description" content="Des créations locales."', false)
            ->assertSee('property="product:price:amount" content="12500.00"', false)
            ->assertSee('property="product:price:currency" content="XOF"', false)
            ->assertSee('name="twitter:image"', false)
            ->assertSee('crossorigin="anonymous"', false)
            ->assertSee('integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"', false);
    }
}
