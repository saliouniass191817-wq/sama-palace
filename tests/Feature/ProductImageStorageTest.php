<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Filesystem\AwsS3V3Adapter as LaravelS3Adapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProductImageStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_upload_persists_its_path_without_an_s3_acl(): void
    {
        config([
            'filesystems.disks.s3.bucket' => 'micro-commerce',
            'filesystems.disks.s3.endpoint' => 'https://project.storage.supabase.co/storage/v1/s3',
            'filesystems.disks.s3.public_url' => null,
        ]);

        $client = Mockery::mock();
        $client->shouldReceive('putObject')
            ->once()
            ->with(Mockery::on(function (array $request): bool {
                $this->assertSame('micro-commerce', $request['Bucket']);
                $this->assertStringStartsWith('products/', $request['Key']);
                $this->assertIsResource($request['Body']);
                $this->assertSame('image/png', $request['ContentType']);
                $this->assertArrayNotHasKey('ACL', $request);

                return true;
            }));

        $s3Disk = Mockery::mock(LaravelS3Adapter::class);
        $s3Disk->shouldReceive('getClient')->andReturn($client);
        Storage::shouldReceive('disk')->with('s3')->andReturn($s3Disk);

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
            'https://project.storage.supabase.co/storage/v1/object/public/micro-commerce/'.$product->image,
            $product->image_url
        );
    }
}
