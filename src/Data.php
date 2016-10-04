<?php
namespace EmailData;

class Data
{
    private $pathBin = '';
    private $pathData = '';

    public function __construct()
    {
        $root = realpath(dirname(__FILE__) . '/../');
        $this->pathBin = $root . '/bin/';
        $this->pathData = $root . '/data/';
    }

    public function getPathToDataFile($format)
    {
        return $this->pathData . 'disposable.' . $format;
    }

    public function loadDomains()
    {
        return $this->loadTextFile($this->pathBin . 'disposable.txt');
    }

    public function loadWhitelist()
    {
        return $this->loadTextFile($this->pathBin . 'whitelist.txt');
    }

    public function loadSources($type, $format)
    {
        return $this->loadTextFile($this->pathBin . 'sources/' . $type . '/' . $format . '.txt');
    }

    private function loadTextFile($path)
    {
        $content = file_get_contents($path);

        if ($content) {
            $content = preg_split('/\r\n|\n\r|\r|\n/', trim($content));
        }

        // Filter comments / blank lines
        $domains = [];
        if (is_array($content)) {
            foreach ($content as $domain) {
                $domain = trim($domain);

                // Too Shoot
                if (mb_strlen($domain) <= 4) {
                    continue;
                }
                // Comment?
                if (mb_substr($domain, 0, 2) === '//') {
                    continue;
                }

                $domains[] = $domain;
            }
        }

        return $domains;
    }
}
