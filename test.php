<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'src/SetUpCache.php';

use martinkz\PrismicCacheTool\SetUpCache;

// The path is relative to the directory containing this file
SetUpCache::init([
    "DOCUMENT_CACHE" => "_docs",
    "IMAGE_CACHE" => "_images",
]);