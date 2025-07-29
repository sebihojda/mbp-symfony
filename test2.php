<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;

//Add an indexing column to a CSV file (column counting rows from 1 to ...)

$in = fopen('php://stdin', 'rb');
$out = fopen('php://stdout', 'wb');

$text = fgets($in);
$string = new UnicodeString($text);
$string = $string->prepend('index,');
fwrite($out, $string);

$i = 1;

while (($text = fgets($in)) !== false) {
    $string = new UnicodeString($text);
    $string = $string->prepend("$i,");
    $i++;
    fwrite($out, $string);
}

fclose($in);
fclose($out);
