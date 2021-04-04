<?php

namespace martinkz\PrismicCacheTool;

require_once 'vendor/autoload.php';

use Prismic\Api;
use Prismic\Predicates;

require_once 'ProcessPrismicDocument.php';


class SetUpCache
{
	private static $DEFAULT_PATH = [
		"DOCUMENT_CACHE" => "%REPOSITORY%/docs/%DOCUMENT_TYPE%/",
		"IMAGE_CACHE" => "%REPOSITORY%/images/%DOCUMENT_TYPE%/",
	];

	public static function init($CACHE_PATH = NULL)
	{
		if (is_null($CACHE_PATH)) {
			$CACHE_PATH = self::$DEFAULT_PATH;
		}

		$data = json_decode(file_get_contents('php://input'));
		$data = json_decode(file_get_contents('webhook-data.json'));

		if ($data) {
			// If masterRef is set, this is a document publish (either regular publish or publish of a scheduled release), or archival of published document
			$hasMasterRef = isset($data->{"masterRef"});
			$hasUpdate = isset($data->{"releases"}->{"update"});
			$hasDeletion = isset($data->{"releases"}->{"deletion"});
			// The below is true if a planned release is being published (and the scheduled document is deleted)
			$isPlannedReleasePublish = $hasMasterRef && $hasDeletion;
			// The below is true if a planned release was saved and scheduled for future publishing
			$isPlannedReleaseSave = !$hasMasterRef && $hasUpdate;
			// The below is true if a planned release was deleted (before it reached the publishing date), 
			// or the release is being deleted after the documents have been published (there appears to be a delay sometimes?)
			$isPlannedReleaseDelete = !$hasMasterRef && !$hasUpdate && $hasDeletion;

			$releaseRef = NULL;
			$documentIDs = NULL;

			if ($hasMasterRef) {
				$releaseRef = $data->{"masterRef"};
				$documentIDs = $data->{"documents"};
			} elseif ($hasUpdate) {
				$releaseInfo = $data->{"releases"}->{"update"}[0];
				if (!empty($releaseInfo->documents)) {
					$releaseRef = $releaseInfo->ref;
					$documentIDs = $releaseInfo->documents;
				}
			}

			if (is_null($releaseRef) || is_null($documentIDs)) exit();

			$api = Api::get($data->{"apiUrl"});
			$response = $api->query(Predicates::in('document.id', $documentIDs), ['ref' => $releaseRef]);
			$documents = $response->results;
			$domain = $data->{"domain"};

			//if( isset($releaseInfo) ) echo "scheduledAt: ". date('Y-m-d H:i:s', substr($releaseInfo->scheduledAt, 0, -3)) . "\n\n";
			//print_r(json_encode($documents));

			foreach ($documents as $document) {
				$CACHE_PATH['DOCUMENT_CACHE'] = str_replace('%REPOSITORY%', $domain, $CACHE_PATH['DOCUMENT_CACHE']);
				$CACHE_PATH['DOCUMENT_CACHE'] = str_replace('%DOCUMENT_TYPE%', $document->{'type'}, $CACHE_PATH['DOCUMENT_CACHE']);
				$CACHE_PATH['IMAGE_CACHE'] = str_replace('%REPOSITORY%', $domain, $CACHE_PATH['IMAGE_CACHE']);
				$CACHE_PATH['IMAGE_CACHE'] = str_replace('%DOCUMENT_TYPE%', $document->{'type'}, $CACHE_PATH['IMAGE_CACHE']);

				$stringAppend = "";
				if ($isPlannedReleaseSave) {
					//$dateTime = date("Y-m-d H:i:s", substr($releaseInfo->scheduledAt, 0, -3));
					$stringAppend = '-' . $releaseInfo->ref;
				}
				// logDebug($document->id . " --- " . $document->uid . $stringAppend);
				//$filename = PRISMIC_CACHE_PATH['DOCUMENT_CACHE'] . $document->type . '/' . $document->uid . $stringAppend . '.json';
				$documentProcessor = new ProcessPrismicDocument($document, $CACHE_PATH, $domain);
				// richText() needs to run before images(). This is because there may be inlined images in the WYSIWYG editor.
				$document = $documentProcessor->richText();
				$document = $documentProcessor->images();

				unset($document['uid']);

				$path = './' . $CACHE_PATH['DOCUMENT_CACHE'] . '/';
				$filename = (isset($document['uid']) ? $document['uid'] : $document['type'] . ' - ' . $document['id']) . $stringAppend . '.json';
				if (!is_dir($path)) {
					mkdir($path, 0755, true);
				}
				file_put_contents($path . $filename, json_encode($document));
			}
		} else {
			$url = "https://asdfgh.cdn.prismic.io/api/v2";
			// $url = "https://mynewsite.prismic.io/api";
			$api = Api::get($url);
			$response = $api->query('');

			$document = $response->results[0];
			$documentProcessor = new ProcessPrismicDocument($document, $CACHE_PATH);
			$document = $documentProcessor->richText();
			$document = $documentProcessor->images(false);

			print_r($document);
		}
	}
}