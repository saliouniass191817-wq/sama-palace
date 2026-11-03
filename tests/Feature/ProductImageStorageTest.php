<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_is_stored_as_a_persistent_unique_s3_path(): void
    {
        Storage::fake('s3');

        config([
            'filesystems.disks.s3.bucket' => 'micro-commerce',
            'filesystems.disks.s3.endpoint' => 'https://project.supabase.co/storage/v1/s3',
            'filesystems.disks.s3.public_url' => null,
        ]);

        $user = User::factory()->create();
        Shop::create([
            'user_id' => $user->id,
            'name' => 'Demo Shop',
            'slug' => 'demo-shop',
            'phone' => '+221770000000',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('products.store'), [
                'name' => 'Sac wax',
                'price' => 12000,
                'description' => 'Produit de test',
                'image' => UploadedFile::fake()->createWithContent('sac.png', base64_decode(
                    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='
                )),
            ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::firstOrFail();

        $this->assertStringStartsWith('products/', $product->image);
        $this->assertNotSame('products', $product->image);
        $this->assertSame(
            'https://project.supabase.co/storage/v1/object/public/micro-commerce/'.$product->image,
            $product->image_url
        );
        Storage::disk('s3')->assertExists($product->image);
    }
}
