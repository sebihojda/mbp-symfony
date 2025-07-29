<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;
use Doctrine\Common\Collections\ArrayCollection;

//Merge two or more CSV files (given they have same number of columns, and if they have a header rows, they match)

function reorder(array $headersOrder, Ds\Map $headersMap, array $headers, $in, $out): void{
    foreach ($headersOrder as $header) {
        if(!$headersMap->hasKey($header->toString())){
            echo 'invalid columns from csv file' . PHP_EOL;
            exit(1);
        }
    }

    while(($text = fgets($in)) !== false){

        $words = new UnicodeString($text)->trim()->split(',');
        for ($i = 0; $i < count($words); ++$i) {
            $headersMap->put($headers[$i]->toString(), $words[$i]->toString());
        }
        #dump($headersMap);
        $string = new UnicodeString('');
        for($i = 0; $i < count($headersOrder); ++$i){
            $word = $headersMap[$headersOrder[$i]->toString()];
            $string =  $string->append("$word,");
        }

        fwrite($out, $string->splice(PHP_EOL, $string->length() - 1));
    }
}

$out = fopen('php://stdout', 'wb');

$in = fopen("./$argv[1]", 'rb');
$text = fgets($in);
$headersOrder = new UnicodeString($text)->trim()->split(',');
fwrite($out, $text);
while (($text = fgets($in)) !== false) {
    fwrite($out, $text);
}
fclose($in);

for($i = 2; $i < $argc; $i++) {
    $in = fopen("./$argv[$i]", 'rb');
    $text = fgets($in);
    $headers = new UnicodeString($text)->trim()->split(',');

    if(count($headersOrder) != count($headers)){
        echo 'invalid number of columns from csv file' . PHP_EOL;
        exit(1);
    }

    $headersMap = new Ds\Map();
    foreach ($headers as $header) {
        $headersMap->put($header->toString(), '');
    }
    #dump($headersOrder);
    #dump($headersMap);
    #dump($headers);
    reorder($headersOrder, $headersMap, $headers, $in, $out);

    fclose($in);
}
fclose($out);
