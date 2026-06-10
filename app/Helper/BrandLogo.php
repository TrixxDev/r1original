<?php

namespace App\Helper;

class BrandLogo
{
    protected static ?array $manifest = null;

    public static function path(?string $slug): ?string
    {
        if (!$slug) {
            return null;
        }

        $slug = strtolower(trim($slug));
        $dir = public_path('images/brand-logos');

        $manifest = self::manifest();
        if (isset($manifest[$slug])) {
            $file = $dir . DIRECTORY_SEPARATOR . $manifest[$slug];
            if (is_file($file)) {
                return $file;
            }
        }

        foreach (['png', 'jpg', 'jpeg', 'webp', 'svg'] as $ext) {
            $file = $dir . DIRECTORY_SEPARATOR . $slug . '.' . $ext;
            if (is_file($file)) {
                return $file;
            }
        }

        return null;
    }

    public static function url(?string $slug): ?string
    {
        $path = self::path($slug);

        if (!$path) {
            return null;
        }

        return '/images/brand-logos/' . rawurlencode(basename($path));
    }

    public static function exists(?string $slug): bool
    {
        return self::path($slug) !== null;
    }

    protected static function manifest(): array
    {
        if (self::$manifest !== null) {
            return self::$manifest;
        }

        $manifestFile = public_path('images/brand-logos/manifest.json');
        if (!is_file($manifestFile)) {
            self::$manifest = [];

            return self::$manifest;
        }

        $decoded = json_decode((string) file_get_contents($manifestFile), true);
        self::$manifest = is_array($decoded) ? $decoded : [];

        return self::$manifest;
    }
}
