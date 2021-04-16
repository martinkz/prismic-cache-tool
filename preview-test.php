<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'src/PrismicPreview.php';

use martinkz\PrismicCacheTool\PrismicPreview;

function getMsnURL($document)
{
    if ($document['type'] === 'msn_window_of_hope') {
        return 'https://windowofhope.makesomenoise.com/';
    }
    return false;
}

$url = PrismicPreview::init('getMsnURL');
$params = [];

parse_str(parse_url($url, PHP_URL_QUERY), $params);

if (isset($params['draft'])) {
    print_r($_SESSION[$params['draft']]);
}