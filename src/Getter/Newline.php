<?php
namespace EmailData\Getter;

use \Curl\Curl;

class Newline
{
    public static function get($url)
    {
        $curl = new Curl();
        $content = $curl->get($url);

        if ($content) {
            $domains = preg_split('/\r\n|\n\r|\r|\n/', trim($content));

            if (is_array($domains)) {
                return $domains;
            }
        }

        return [];
    }
}
