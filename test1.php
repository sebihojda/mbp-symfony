<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;

//Prepend CSV file with a header row (row with column names)

$in = fopen('php://stdin', 'rb');
$out = fopen('php://stdout', 'wb');

$noHeaders = 0;
if($argc > 1){
    $headers = new UnicodeString('');
    for($i = 1; $i < $argc; $i++){
        $headers = $headers->append("$argv[$i],");
    }
    fwrite($out, $headers->splice(PHP_EOL, $headers->length() - 1));
}
else
    $noHeaders = 1;

while (!feof($in)) {
    $text = fgets($in);

    if($noHeaders){
        $string = new UnicodeString($text);
        $count = count($string->split(','));
        $headers = new UnicodeString('');
        for($i = 0; $i < $count; $i++){
            $headers = $headers->append("column$i,");
        }
        fwrite($out, $headers->splice(PHP_EOL, $headers->length() - 1));
        $noHeaders = 0;
    }

    fwrite($out, $text);
}

fclose($in);
fclose($out);
