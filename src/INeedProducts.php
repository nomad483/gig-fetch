<?php

declare(strict_types=1);

namespace Src;

use SimpleXMLElement;

// TODO(me): Divide the class into several groups according to areas of responsibility
class INeedProducts
{
    public static function getProductsFromGig(string $url, array $categories = [], bool $cache = true): array
    {
        if (empty($url)) {
            return [];
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            error_log('Invalid URL provided: ' . $url);
            return [];
        }

        $content = null;
        $cacheProvider = new CacheProvider();
        $cacheStoreKey = md5($url);

        if ($cache) {
            $content = $cacheProvider->get($cacheStoreKey);
        }

        if (empty($content)) {
            $content = new FetchProvider()->fetch($url);

            if (empty($content)) {
                return [];
            }

            if ($cache) {
                $cacheProvider->store($content, $cacheStoreKey);
            }
        }

        $xml = new SimpleXMLElement($content);

        $products = [];

        foreach ($xml->shop->offers->offer as $offer) {
            //            print_r($offer);
            //            die();
            if (!empty($categories) && !in_array((int)$offer->categoryId, $categories, true)) {
                continue;
            }

            $groupId = (int)$offer['group_id'];

            if (!isset($products[$groupId])) {
                $title = !empty(trim((string)$offer->name_ua)) ? trim((string)$offer->name_ua) : trim((string)$offer->name);
                $description = !empty(trim((string)$offer->description_ua)) ? trim((string)$offer->description_ua) : trim((string)$offer->description);

                $products[$groupId] = [
                    'title' => $title,
                    'description' => $description,
                    'price' => (int)$offer->price,
                    'in_stock' => (string)$offer['available'],
                    'modifications' => []
                ];
            }

            foreach ($offer->param as $param) {
                if ((string)$param['name'] === 'Розмірна сітка') {
                    $all_sizes = explode(',', (string)$param);

                    foreach ($all_sizes as $size) {
                        // так як в xml не має кількості по розмірах
                        // то просто беремо розмір і ставимо 1
                        if (!empty(trim($size))) {
                            $products[$groupId]['modifications'][trim($size)] = 1;
                        }
                    }
                }

                if ((string)$param['name'] === 'Розмір') {
                    $size = trim((string)$param);
                    if (!empty(trim($size))) {
                        $products[$groupId]['modifications'][$size] = 1;
                    }
                }
            }
        }

        return array_values($products);
    }
}
