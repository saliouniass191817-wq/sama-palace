<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $user = User::factory()->create([
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'phone' => '+221771234567',
            'address' => 'Dakar, Senegal',
            'password' => 'password',
        ]);

        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Awa Boutique',
            'slug' => 'awa-boutique',
            'description' => 'Mode, accessoires et petits articles pour livraison locale.',
            'phone' => '+221771234567',
        ]);

        Product::create([
            'shop_id' => $shop->id,
            'name' => 'Sac wax',
            'price' => 7500,
            'description' => 'Sac fait main avec tissu wax.',
        ]);

        Product::create([
            'shop_id' => $shop->id,
            'name' => 'Sandales femme',
            'price' => 12000,
            'description' => 'Sandales confortables disponibles en plusieurs tailles.',
        ]);
    }
}
