<?php

namespace martinkz\PrismicCacheTool;

require_once 'vendor/autoload.php';

use Prismic\Api;
use Prismic\LinkResolver;
use Prismic\Predicates;


class SetUpCache
{
    public static function init()
    {
    	$url = "https://asdfgh.cdn.prismic.io/api/v2";
		// $url = "https://mynewsite.cdn.prismic.io/api/v2";
		$api = Api::get($url);
		$response = $api->query('');
    	return json_encode($response);
    }
}
