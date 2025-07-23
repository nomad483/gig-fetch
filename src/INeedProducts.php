<?php

declare(strict_types=1);

namespace Src;

use SimpleXMLElement;

// TODO(me): Divide the class into several groups according to areas of responsibility
class INeedProducts
{
    const string CACHE_DIR = ROOT_DIR . '/cache';
    const string CACHE_FILE_NAME = 'products.xml';
    /**
     * Cache lifetime in seconds.
     * Set to 3600 seconds (1 hour) by default.
     */
    const int CACHE_LIFETIME = 3600;
    const string CACHE_FILE = self::CACHE_DIR . '/' . self::CACHE_FILE_NAME;

    public static function getProductsFromGig(array $categories = []): array
    {
        $xml = new SimpleXMLElement(static::getXml());

        $products = [];

        foreach ($xml->shop->offers->offer as $offer) {
            if (!empty($categories) && !in_array((int)$offer->categoryId, $categories, true)) {
                continue;
            }

            $modifications = [];

            foreach ($offer->param as $param) {
                if ((string)$param['name'] === 'Розмірна сітка') {
                    $all_sizes = explode(',', (string)$param);

                    foreach ($all_sizes as $size) {
                        // так як в xml не має кількості по розмірах
                        // то просто беремо розмір і ставимо 1
                        $modifications[trim($size)] = 1;
                    }
                    break;
                }
            }

            $products[] = [
                'title' => (string)$offer->name,
                'description' => trim((string)$offer->description),
                'price' => (float)$offer->price,
                'in_stock' => (string)$offer['available'],
                'modifications' => $modifications
            ];
        }

        return $products;
    }

    private static function getXml(bool $cache = true): false|string
    {
        if ($cache && $content = static::getCachedContent()) {
            return $content;
        }

        $content = file_get_contents('https://gigmilitary.com/feeds/yml-feed.xml');

        if ($content === false) {
            throw new \RuntimeException('Failed to fetch content from the URL');
        }

        if ($cache) {
            if (!is_dir(self::CACHE_DIR)) {
                mkdir(self::CACHE_DIR, 0755, true);
            }

            file_put_contents(self::CACHE_FILE, $content);
        }

        return $content;
    }

    private static function getCachedContent(): false|string|null
    {
        if (file_exists(self::CACHE_FILE) && (time() - filemtime(self::CACHE_FILE)) < self::CACHE_LIFETIME) {
            return file_get_contents(self::CACHE_FILE);
        }

        return null;
    }
}