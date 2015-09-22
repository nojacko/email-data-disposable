<?php
namespace EmailData\Exporter;

class Php implements BaseInterface
{
    public static function save($path, $data)
    {
        $output = '<?php ' . PHP_EOL;
        $output .= 'return ' . preg_replace('/\s+[0-9]+\s+=>\s+/i', PHP_EOL . '    ', var_export($data, true));
        $output .= ';' . PHP_EOL;;

        file_put_contents($path, $output);
    }
}
