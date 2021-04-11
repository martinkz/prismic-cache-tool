<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'src/PrismicPreview.php';

use martinkz\PrismicCacheTool\PrismicPreview;

function getPreviewURL($document)
{
    $BRAND_URLS = [
        "capital" => "https://win.capitalfm.com/",
        "heart" => "https://win.heart.co.uk/",
        "radiox" => "https://win.radiox.co.uk/",
        "lbc" => "https://win.lbc.co.uk/",
        "classic" => "https://win.classicfm.com/",
        "gold" => "https://win.mygoldmusic.co.uk/",
        "smooth" => "https://win.smoothradio.com/",
        "xtra" => "https://win.capitalxtra.com/"
    ];
    if ($document['type'] === 'enhanced_article_page') {
        $brand = strtolower($document['data']['brand']);
        if (isset($brand)) {
            return $BRAND_URLS[$brand] . 'comp/' . $document['uid'];
        } else {
            throw new \Exception('Brand field missing');
        }
    }
    return false;
}

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