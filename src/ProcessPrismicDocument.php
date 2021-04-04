<?php

// richText() needs to run before images(). This is because there may be inlined images in the WYSIWYG editor.
// Needs to be fixed so that inline images are also cached.

namespace martinkz\PrismicCacheTool;

use Prismic\Dom\RichText;

class ProcessPrismicDocument
{
    private $document = [];
    private $richTextRefs = [];
    private $imageRefs = [];
    private $domain = NULL;
    private $CACHE_PATH = NULL;

    function __construct($document, $CACHE_PATH, $domain = NULL)
    {
        // Convert stdClass to Array
        $this->document = json_decode(json_encode($document), true);
        $this->domain = $domain;
        $this->CACHE_PATH = $CACHE_PATH;
        $this->array_walk_recursive_array($this->document['data']);
    }

    private function copyFile($url, $path)
    {
        if (!@copy($url, $path)) {
            $errors = error_get_last();
            echo "COPY ERROR: " . $errors['type'];
            echo "<br />\n" . $errors['message'];
            return false;
        } else {
            return true;
        }
    }

    private function array_walk_recursive_array(array &$arr, &$parent = NULL)
    {
        foreach ($arr as $k => &$v) {
            if (is_array($v)) {
                if ($k === 'spans') {
                    if (end($this->richTextRefs) !== $parent) {
                        $this->richTextRefs[] = &$parent;
                    }
                } elseif ($k === 'dimensions') {
                    //echo json_encode($arr) . PHP_EOL;
                    $this->imageRefs[] = &$arr;
                }
                $this->array_walk_recursive_array($v, $arr);
            }
        }

        // print_r($this->imageRefs);
        // print_r($this->richTextRefs);
    }


    public function richText()
    {
        foreach ($this->richTextRefs as $i => $v) {
            $this->richTextRefs[$i] = RichText::asHtml(json_decode(json_encode($this->richTextRefs[$i])));
        }
        return $this->document;
    }

    public function images($cache = true)
    {
        foreach ($this->imageRefs as $i => $v) {
            $imageURL = $this->imageRefs[$i]['url'];

            if ($cache) {
                $fileName = basename(parse_url($imageURL, PHP_URL_PATH));
                $path = $this->CACHE_PATH['IMAGE_CACHE'] . (!is_null($this->domain) ? $this->domain . '/' : '') . $this->document['type'] . '/';
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
                $imagePath = $path . $fileName;

                if (!file_exists($imagePath)) {
                    if ($this->copyFile($imageURL, $imagePath)) {
                        $imageURL = $imagePath;
                    }
                }
            }

            $this->imageRefs[$i] = $imageURL;
        }
        return $this->document;
    }
}