<?php

namespace App\Support;

use Illuminate\Filesystem\AwsS3V3Adapter as LaravelS3Adapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class PublicMedia
{
    public static function store(UploadedFile $file, string $directory, string $disk = 's3'): string
    {
        // Supabase Storage controls public reads at the bucket level. Sending
        // an S3 object ACL (via Flysystem's `visibility` option) is unsupported.
        $storage = Storage::disk($disk);

        if (! $storage instanceof LaravelS3Adapter) {
            throw new RuntimeException('The configured media disk must use the S3 driver.');
        }

        $path = $file->hashName($directory);
        $image = self::prepareImage($file);
        $stream = fopen('php://temp', 'w+b');

        if ($stream === false) {
            throw new RuntimeException('A temporary image stream could not be created.');
        }

        if (fwrite($stream, $image) === false) {
            fclose($stream);

            throw new RuntimeException('The processed image could not be written.');
        }
        rewind($stream);

        try {
            // Flysystem's S3 adapter always adds an ACL (private by default).
            // Supabase Storage rejects x-amz-acl, so issue PutObject directly
            // through the configured S3 client without an ACL parameter.
            $storage->getClient()->putObject([
                'Bucket' => config("filesystems.disks.{$disk}.bucket"),
                'Key' => $path,
                'Body' => $stream,
                'ContentType' => $file->getMimeType() ?: 'application/octet-stream',
            ]);
        } finally {
            fclose($stream);
        }

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The file could not be stored.');
        }

        return $path;
    }

    private static function prepareImage(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();
        $encoder = match ($mimeType) {
            'image/jpeg' => 'imagejpeg',
            'image/png' => 'imagepng',
            'image/webp' => 'imagewebp',
            default => throw new RuntimeException('The uploaded image format is not supported.'),
        };

        if (! function_exists($encoder)) {
            throw new RuntimeException('GD does not support the uploaded image format.');
        }

        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw new RuntimeException('The uploaded image could not be read.');
        }

        if (! function_exists('imagecreatefromstring')) {
            throw new RuntimeException('GD is required to process uploaded images.');
        }

        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            throw new RuntimeException('The uploaded image could not be decoded.');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, 1200 / max($sourceWidth, $sourceHeight));
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));
        $processed = imagecreatetruecolor($width, $height);

        if ($processed === false) {
            imagedestroy($source);

            throw new RuntimeException('The processed image could not be allocated.');
        }

        if ($mimeType !== 'image/jpeg') {
            imagealphablending($processed, false);
            imagesavealpha($processed, true);
            imagefill($processed, 0, 0, imagecolorallocatealpha($processed, 0, 0, 0, 127));
        }

        if (! imagecopyresampled($processed, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight)) {
            imagedestroy($source);
            imagedestroy($processed);

            throw new RuntimeException('The uploaded image could not be resized.');
        }

        ob_start();
        $encoded = match ($mimeType) {
            'image/jpeg' => imagejpeg($processed, null, 85),
            'image/png' => imagepng($processed, null, 6),
            'image/webp' => imagewebp($processed, null, 85),
        };
        $output = ob_get_clean();
        imagedestroy($source);
        imagedestroy($processed);

        if (! $encoded || ! is_string($output)) {
            throw new RuntimeException('The uploaded image could not be re-encoded.');
        }

        return $output;
    }

    public static function storeForForm(UploadedFile $file, string $directory, string $field, string $disk = 's3'): string
    {
        try {
            return self::store($file, $directory, $disk);
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            foreach (['key', 'secret'] as $credential) {
                $value = config("filesystems.disks.{$disk}.{$credential}");

                if (is_string($value) && $value !== '') {
                    $message = str_replace($value, '[redacted]', $message);
                }
            }

            Log::warning('Unable to store uploaded media.', [
                'disk' => $disk,
                'directory' => $directory,
                'exception' => $exception::class,
                'message' => $message,
            ]);

            throw ValidationException::withMessages([
                $field => __('messages.media.store_failed'),
            ]);
        }
    }

    public static function delete(?string $value, string $disk = 's3'): void
    {
        try {
            $path = self::path($value);

            if ($path && ! Storage::disk($disk)->delete($path)) {
                Log::warning('Unable to delete stored media.', [
                    'disk' => $disk,
                    'path' => $path,
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('Unable to delete stored media.', [
                'disk' => $disk,
                'exception' => $exception::class,
            ]);
        }
    }

    public static function storeAndPersist(
        UploadedFile $file,
        string $directory,
        string $field,
        callable $persist,
        ?string $replacedValue = null,
        string $disk = 's3',
    ): string {
        $newPath = self::storeForForm($file, $directory, $field, $disk);

        try {
            if ($persist($newPath) === false) {
                throw new RuntimeException('The uploaded file could not be attached to its record.');
            }
        } catch (Throwable $exception) {
            self::delete($newPath, $disk);

            throw $exception;
        }

        if ($replacedValue && $replacedValue !== $newPath) {
            self::delete($replacedValue, $disk);
        }

        return $newPath;
    }

    public static function url(?string $value, string $disk = 's3'): ?string
    {
        if (! $value) {
            return null;
        }

        if ($disk === 's3' && $path = self::path($value)) {
            if ($publicUrl = self::s3PublicUrl($path)) {
                return $publicUrl;
            }
        }

        if (self::isUrl($value)) {
            return $value;
        }

        if ($disk === 's3' && $publicUrl = self::s3PublicUrl($value)) {
            return $publicUrl;
        }

        return Storage::disk($disk)->url($value);
    }

    public static function path(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (! self::isUrl($value)) {
            return ltrim(Str::after($value, 'storage/'), '/');
        }

        return self::pathFromConfiguredPublicUrl($value)
            ?? self::pathFromSupabaseUrl($value);
    }

    private static function s3PublicUrl(string $path): ?string
    {
        if ($baseUrl = config('filesystems.disks.s3.public_url')) {
            return rtrim($baseUrl, '/').'/'.ltrim($path, '/');
        }

        $endpoint = self::supabaseBaseEndpoint();
        $bucket = config('filesystems.disks.s3.bucket');

        if (! $endpoint || ! $bucket) {
            return null;
        }

        return $endpoint.'/storage/v1/object/public/'.$bucket.'/'.ltrim($path, '/');
    }

    private static function pathFromConfiguredPublicUrl(string $url): ?string
    {
        $baseUrl = config('filesystems.disks.s3.public_url');

        if (! $baseUrl) {
            return null;
        }

        $prefix = rtrim($baseUrl, '/').'/';

        return str_starts_with($url, $prefix)
            ? ltrim(substr($url, strlen($prefix)), '/')
            : null;
    }

    private static function pathFromSupabaseUrl(string $url): ?string
    {
        $bucket = config('filesystems.disks.s3.bucket');

        if (! $bucket) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path)) {
            return null;
        }

        $pattern = '#/(?:s3/)?storage/v1/object/public/'.preg_quote($bucket, '#').'/(.+)$#';

        if (! preg_match($pattern, $path, $matches)) {
            return null;
        }

        return urldecode($matches[1]);
    }

    private static function supabaseBaseEndpoint(): ?string
    {
        $endpoint = config('filesystems.disks.s3.endpoint');

        if (! $endpoint || (! str_contains($endpoint, 'supabase.co') && ! str_contains($endpoint, '/storage/v1'))) {
            return null;
        }

        return preg_replace('#/storage/v1(?:/s3)?$#', '', rtrim($endpoint, '/'));
    }

    private static function isUrl(string $value): bool
    {
        return Str::startsWith($value, ['http://', 'https://', '//']);
    }
}
