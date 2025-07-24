<?php

namespace Src;

enum UrlEnum
{
    case GIG_PRODUCTS;
    case KIBORG_PRODUCTS;

    public function getUrl(): string
    {
        return match ($this) {
            self::GIG_PRODUCTS => 'https://gigmilitary.com/feeds/yml-feed.xml',
            self::KIBORG_PRODUCTS => 'https://kiborg.com.ua/content/export/361c7a630acf3b9780a738a7b3f44786.xml?1747206423780',
        };
    }
}
