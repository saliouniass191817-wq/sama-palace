<?php

namespace Tests\Unit;

use App\Support\PublicMedia;
use Illuminate\Filesystem\AwsS3V3Adapter as LaravelS3Adapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class PublicMediaTest extends TestCase
{
    public function test_it_extracts_paths_from_current_and_legacy_supabase_urls(): void
    {
        config([
            'filesystems.disks.s3.bucket' => 'micro-commerce',
            'filesystems.disks.s3.endpoint' => 'https://project.supabase.co/storage/v1/s3',
            'filesystems.disks.s3.public_url' => null,
        ]);

        $this->assertSame(
            'products/image.jpg',
            PublicMedia::path('https://project.supabase.co/storage/v1/object/public/micro-commerce/products/image.jpg')
        );

        $this->assertSame(
            'products/image.jpg',
            PublicMedia::path('https://project.supabase.co/s3/storage/v1/object/public/micro-commerce/products/image.jpg')
        );
    }

    public function test_it_deletes_a_new_upload_when_persisting_its_record_fails(): void
    {
        $uploadedPath = null;
        $client = Mockery::mock();
        $client->shouldReceive('putObject')->once();

        $disk = Mockery::mock(LaravelS3Adapter::class);
        $disk->shouldReceive('getClient')->andReturn($client);
        $disk->shouldReceive('delete')
            ->once()
            ->with(Mockery::on(function (string $path) use (&$uploadedPath): bool {
                return $path === $uploadedPath;
            }));

        Storage::shouldReceive('disk')->with('s3')->andReturn($disk);

        try {
            PublicMedia::storeAndPersist(
                UploadedFile::fake()->createWithContent('product.png', 'image contents'),
                'products',
                'image',
                function (string $path) use (&$uploadedPath): bool {
                    $uploadedPath = $path;

                    throw new RuntimeException('Database write failed.');
                },
            );
        } catch (RuntimeException $exception) {
            // The original persistence error is rethrown after storage cleanup.
            $this->assertSame('Database write failed.', $exception->getMessage());
        }

        $this->assertNotNull($uploadedPath);
        $disk->shouldHaveReceived('delete')->once();
    }
}
