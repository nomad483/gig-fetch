<?php

require __DIR__ . '/vendor/autoload.php';

use Src\INeedProducts;
use Src\UrlEnum;

const ROOT_DIR = __DIR__;

die(print_r(INeedProducts::getProductsFromGig(url: UrlEnum::KIBORG_PRODUCTS->getUrl()), true));
