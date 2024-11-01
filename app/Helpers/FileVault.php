<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileVault
{
    /**
     * Decrypt content
     *
     * @return mixed
     */
    public static function decrypt(string $path)
    {
        return decrypt(Storage::get($path));
    }

    /**
     * Encrypt content
     */
    public static function encrypt(string $content, string $dir = 'vault'): string
    {
        Storage::put($path = static::hashName($dir), encrypt($content));

        return $path;
    }

    /**
     * Get a filename for the file.
     *
     * @param  string  $extension
     */
    public static function hashName(string $directory): string
    {
        return rtrim($directory, '/') . '/' . Str::random(40) . '.enc';
    }
}
