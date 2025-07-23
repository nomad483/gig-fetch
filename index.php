<?php

require __DIR__ . '/vendor/autoload.php';

use Src\INeedProducts;


const ROOT_DIR = __DIR__;

die(print_r(INeedProducts::getProductsFromGig([1, 65, 733]), true));