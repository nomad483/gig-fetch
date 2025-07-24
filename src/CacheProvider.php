<?php

namespace Src;

class CacheProvider
{
    public const string CACHE_DIR = ROOT_DIR . '/cache';
    /**
     * Cache lifetime in seconds.
     * Set to 3600 seconds (1 hour) by default.
     */
    public const int CACHE_LIFETIME = 3600;

    public function store($xml, string $key = 'gig'): bool
    {
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
        }

        $cacheFile = self::CACHE_DIR . '/' . $key . '.xml';

        try {
            file_put_contents($cacheFile, $xml);

            return true;
        } catch (\Throwable $exception) {
            // Handle the exception (e.g., log it)
            error_log('Failed to store cache: ' . $exception->getMessage());
            return false;
        }
    }

    public function get(string $key = 'gig'): ?string
    {
        $cacheFile = self::CACHE_DIR . '/' . $key . '.xml';

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < self::CACHE_LIFETIME) {
            return file_get_contents($cacheFile);
        }

        return null;
    }
}
