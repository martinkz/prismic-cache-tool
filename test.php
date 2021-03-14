<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// date_default_timezone_set("Europe/London");

// function logDebug($thing)
// {
//     $path = 'log.txt';
//     file_put_contents($path, date('Y-m-d, H:i:s') . ' --- ' . json_encode($thing) . "\n", FILE_APPEND);
// }

// require_once 'vendor/autoload.php';

// use Prismic\Api;
// use Prismic\LinkResolver;
// use Prismic\Predicates;


include 'src/SetUpCache.php';
use martinkz\PrismicCacheTool\SetUpCache;
print_r(SetUpCache::init());



// $url = "https://asdfgh.cdn.prismic.io/api/v2";
// // $url = "https://mynewsite.cdn.prismic.io/api/v2";
// $api = Api::get($url);
// $response = $api->query('');


// echo '<pre>' . json_encode($response) . '</pre>';

