<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;
use Doctrine\Common\Collections\ArrayCollection;

//Perform column reordering based on a given sequence of column headers

$in = fopen('php://stdin', 'rb');
$out = fopen('php://stdout', 'wb');

$text = fgets($in);
$headers = new UnicodeString($text)->trim()->split(',');

$headersMap = new Ds\Map();
foreach ($headers as $header) {
    $headersMap->put($header->toString(), '');
}

if($argc - 1 == count($headers)){
    $orders = new ArrayCollection();
    for($i = 1; $i < $argc; $i++){
        $orders[$i] = $argv[$i];
    }

    foreach ($orders as $order) {
        if(!$headersMap->hasKey($order)){
            echo 'invalid order input' . PHP_EOL;
            exit(1);
        }
    }

    $ordersString = new UnicodeString('');
    for($i = 1; $i < $argc; ++$i){
        $ordersString =  $ordersString->append("$orders[$i],");
    }
    $ordersString = $ordersString->splice(PHP_EOL, $ordersString->length() - 1);
    fwrite($out, $ordersString);

    while(($text = fgets($in)) !== false){
        $words = new UnicodeString($text)->trim()->split(',');
        for ($i = 0; $i < count($words); ++$i) {
            $headersMap->put($headers[$i]->toString(), $words[$i]->toString());
        }

        $string = new UnicodeString('');
        for($i = 1; $i < $argc; ++$i){
            $word = $headersMap[$orders[$i]];
            $string =  $string->append("$word,");
        }
        $string = $string->splice(PHP_EOL, $string->length() - 1);
        fwrite($out, $string);
    }
}else{
    echo 'insufficient order input' . PHP_EOL;
    fwrite($out, $text);
    while (($text = fgets($in)) !== false) {
        fwrite($out, $text);
    }
}

fclose($in);
fclose($out);