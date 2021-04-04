<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


include 'src/SetUpCache.php';

use martinkz\PrismicCacheTool\SetUpCache;

SetUpCache::init([
    "DOCUMENT_CACHE" => "cache/docs/",
    "IMAGE_CACHE" => "cache/images/",
]);