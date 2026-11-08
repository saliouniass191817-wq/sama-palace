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
        $stream = fopen($file->getRealPath(), 'rb');

        if ($stream === false) {
            throw new RuntimeException('The uploaded file could not be read.');
        }

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
                $field => 'Impossible de stocker cette image. Vérifiez la configuration du stockage.',
            ]);
        }
    }

    public static function delete(?string $value, string $disk = 's3'): void
    {
        $path = self::path($value);

        if ($path) {
            Storage::disk($disk)->delete($path);
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
