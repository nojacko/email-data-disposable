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

    public function loadTextFile($path)
    {
        $content = file_get_contents($path);

        if ($content) {
            $content = preg_split('/\r\n|\r|\n/', trim($content));
        }

        return is_array($content) ? $content : [];
    }
}
