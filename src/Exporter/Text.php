<?php
namespace EmailData\Exporter;

class Text
{
    public static function save($path, $data)
    {
        $domainsTxt = implode("\n", $data);
        file_put_contents($path, $domainsTxt);
    }
}
