<?php
namespace EmailData\Exporter;

class Json
{
    public static function save($path, $data)
    {
        file_put_contents($path, json_encode($data));
    }
}
