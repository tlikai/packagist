<?php

namespace Packagist\Helpers;

use Requests;

class Downloader
{

    public static $basePath;

    public static $baseUrl = 'https://packagist.org/';

    public static $expires = 3600;

    public static $headers = [
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36',
    ];

    public static $options = [
        'timeout' => 60,
        'connect_timeout' => 60,
    ];

    public static function fetch($file)
    {
        if (!static::download($file)) {
            return false;
        }

        $file = static::getAbsoluteFile($file);
        return file_get_contents($file);
    }

    public static function getAbsoluteFile($file)
    {
        return static::$basePath . ltrim($file, '/');
    }

    public static function getAbsoluteUrl($file)
    {
        return static::$baseUrl . ltrim($file, '/');
    }

    public static function download($file)
    {
        if (!static::isExpired($file)) {
            return true;
        }

        $url = static::getAbsoluteUrl($file);
        $response = Requests::get($url, static::$headers, static::$options);
        if ($response->status_code != 200) {
            return false;
        }

        $content = $response->body;
        return static::save($file, $content);
    }

    public static function save($file, $content)
    {
        $file = static::getAbsoluteFile($file);
        $path = dirname($file);
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return file_put_contents($file, $content);
    }

    public static function isExpired($file)
    {
        $file = static::getAbsoluteFile($file);
        if (file_exists($file) && filemtime($file) + static::$expires > time()) {
            return false;
        }

        return true;
    }

}
