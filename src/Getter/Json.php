<?php
namespace EmailData\Getter;

use \Curl\Curl;

class Json
{
    public static function get($url)
    {
        $curl = new Curl();

        $content = $curl->get($url);
        if ($content) {
            $domains = json_decode($content, true);

            if (isset($domains['hosts'])) {
                $domains = $domains['hosts'];
            }

            if (is_array($domains)) {
                return $domains;
            }
        }

        return [];
    }
}
