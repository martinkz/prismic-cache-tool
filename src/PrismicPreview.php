<?php

namespace martinkz\PrismicCacheTool;

require_once 'vendor/autoload.php';

use Prismic\Api;
use Prismic\Predicates;

require_once 'ProcessPrismicDocument.php';

class PrismicPreview
{
    public static function init($getPreviewURL = NULL)
    {

        if (is_null($getPreviewURL)) {
            echo "Error. No URL resolver function passed.";
            return;
        }

        if (isset($_GET['token'])) {

            $hostname = parse_url($_GET['token'], PHP_URL_HOST);

            // Preview token
            // Hopefully the URL structure for the preview token won't change
            // https://asdfgh.prismic.io/previews/X4rJpxAAACEA_4M1?websitePreviewId=X4mI6hAAACAA-exI // Live page
            // https://asdfgh.prismic.io/previews/X4sAThAAAKsGAHVl~X4rJpxAAACEA_4M1?websitePreviewId=X4mI6hAAACAA-exI // Scheduled release (~)
            // https://asdfgh.prismic.io/previews/X4rJpxAAACEA_4M1:X4sA3xAAACEAAHfo?websitePreviewId=X4mI6hAAACAA-exI // Saved draft or modified document saved, but not yet published (:)

            $tokenPath = parse_url($_GET['token'], PHP_URL_PATH);
            $ref = substr(strrchr($tokenPath, "/"), 1);

            $PREVIEW_TYPE = [
                'live' => false,
                'scheduled_release' => false,
                'draft_save' => false
            ];

            $PREVIEW_TYPE['live'] = strlen($ref) === 16;
            if (!$PREVIEW_TYPE['live']) {
                $PREVIEW_TYPE['scheduled_release'] = $ref[16] === '~';
                $PREVIEW_TYPE['draft_save'] = $ref[16] === ':';
            }

            if (!($PREVIEW_TYPE['live'] || $PREVIEW_TYPE['scheduled_release'] || $PREVIEW_TYPE['draft_save'])) {
                echo "Error. Unknown preview type";
                exit();
            }

            $repo_url = "https://{$hostname}/api/v2";
            $api = Api::get($repo_url);
            $response = $api->query(Predicates::at('document.id', $_GET['documentId']), ['ref' => $_GET['token']]);
            $document = $response->results[0];
            $document = json_decode(json_encode($document), true);

            if ($PREVIEW_TYPE['draft_save']) {
                $documentProcessor = new ProcessPrismicDocument($document);
                $document = $documentProcessor->richText();
                $document = $documentProcessor->images(false);
                session_start();
                $_SESSION[$ref] = json_encode($document);
            }

            $urlAppend = $PREVIEW_TYPE['draft_save'] ? "?draft=" . $ref : ($PREVIEW_TYPE['scheduled_release'] ? "?release=" . $ref : "");

            $url = $getPreviewURL($document);

            if ($url) {
                $redirect_url = $url . $urlAppend;
                header('Location: ' . $redirect_url);
            } else {
                echo "Error. Couldn't resolve the page URL for this document.";
            }
        }
    }
}