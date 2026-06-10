<?php

namespace App\Helper;

class AssetHelper
{
    public static function v(string $path): string
    {
        $normalized = ltrim($path, '/');
        $fullPath = public_path($normalized);
        $version = is_file($fullPath) ? (string) filemtime($fullPath) : '1';

        return asset($normalized) . '?v=' . $version;
    }
}
