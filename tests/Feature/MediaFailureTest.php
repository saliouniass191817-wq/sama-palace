<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Filesystem\AwsS3V3Adapter as LaravelS3Adapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaFailureTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_product_image_replacement_keeps_the_previous_file_and_database_path(): void
    {
        $disk = Storage::fake('s3');
        $disk->put('products/old-image.png', 'old image');

        $user = User::factory()->create();
        $shop = $this->createShop($user);
        $product = $shop->products()->create([
            'name' => 'Sac wax',
            'price' => 12000,
            'image' => 'products/old-image.png',
        ]);

        $this->makeStorageFail();

        $response = $this->actingAs($user)->put(route('products.update', $product), [
            'name' => $product->name,
            'price' => $product->price,
            'image' => $this->uploadedImage(),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'image' => 'products/old-image.png',
        ]);
        $disk->assertExists('products/old-image.png');
    }

    public function test_failed_shop_logo_replacement_keeps_the_previous_file_and_database_path(): void
    {
        $disk = Storage::fake('s3');
        $disk->put('shops/old-logo.png', 'old logo');

        $user = User::factory()->create();
        $shop = $this->createShop($user, ['logo' => 'shops/old-logo.png']);

        $this->makeStorageFail();

        $response = $this->actingAs($user)->put(route('shops.update'), [
            'name' => $shop->name,
            'phone' => $shop->phone,
            'logo' => $this->uploadedImage(),
        ]);

        $response->assertSessionHasErrors('logo');
        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'logo' => 'shops/old-logo.png',
        ]);
        $disk->assertExists('shops/old-logo.png');
    }

    private function createShop(User $user, array $attributes = []): Shop
    {
        return Shop::create($attributes + [
            'user_id' => $user->id,
            'name' => 'Demo Shop',
            'slug' => 'demo-shop-'.$user->id,
            'phone' => '+221770000000',
        ]);
    }

    private function makeStorageFail(): void
    {
        $client = \Mockery::mock();
        $client->shouldReceive('putObject')->once()->andThrow(new \RuntimeException('S3 is unavailable.'));

        $disk = \Mockery::mock(LaravelS3Adapter::class);
        $disk->shouldReceive('getClient')->andReturn($client);

        Storage::shouldReceive('disk')->with('s3')->andReturn($disk);
    }

    private function uploadedImage(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'new.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
        );
    }
}
