<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("Europe/London");

function logDebug($thing)
{
    $path = 'log.txt';
    file_put_contents($path, date('Y-m-d, H:i:s') . ' --- ' . json_encode($thing) . "\n", FILE_APPEND);
}

require_once 'vendor/autoload.php';

use Prismic\Api;
use Prismic\LinkResolver;
use Prismic\Predicates;
//use Prismic\Dom\RichText;

//require_once 'ProcessPrismicDocument.php';
//require_once 'config.php';



//$data = json_decode(file_get_contents('webhook-data.json'));
//$data = false;

$url = "https://asdfgh.cdn.prismic.io/api/v2";
// $url = "https://mynewsite.cdn.prismic.io/api/v2";
$api = Api::get($url);
$response = $api->query('');
// $document = $response->results[0];
//$documentProcessor = new ProcessPrismicDocument($document);
// $document = $documentProcessor->images();
//$document = $documentProcessor->richText();

echo '<pre>' . json_encode($response) . '</pre>';
// print_r($document['data']['body'][0]['primary']['intro_title']);
// print_r($document['data']['body'][0]['primary']['intro_copy']);
