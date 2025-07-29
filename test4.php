<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;
use Doctrine\Common\Collections\ArrayCollection;

//Perform column removal by given name or index

$in = fopen('php://stdin', 'rb');
$out = fopen('php://stdout', 'wb');

$text = fgets($in);
$headers = new UnicodeString($text)->trim()->split(',');
$headerToRemove = new UnicodeString('');
if($argc == 2){
    if(is_numeric($argv[1])){
        if($argv[1] >= 0 && $argv[1] < count($headers)){
            $headerToRemove = $headers[$argv[1]];
        }else{
            echo 'invalid numeric argument' . PHP_EOL;
            exit(1);
        }
    }else{
        $headerToRemove = new UnicodeString($argv[1]);
    }
}else{
    echo 'invalid number of arguments' . PHP_EOL;
    exit(1);
}

$newHeaders = new UnicodeString('');
foreach($headers as $header){
    if($header != $headerToRemove){
        $newHeaders = $newHeaders->append("$header,");
    }
}
fwrite($out, $newHeaders->splice(PHP_EOL, $newHeaders->length() - 1));

while (($text = fgets($in)) !== false) {
    $words = new UnicodeString($text)->trim()->split(',');
    $newWords = new UnicodeString('');
    $index = 0;
    foreach($headers as $header){
        if($header != $headerToRemove){
            $newWords = $newWords->append("$words[$index],");
        }
        $index++;
    }
    fwrite($out, $newWords->splice(PHP_EOL, $newWords->length() - 1));
}

fclose($in);
fclose($out);