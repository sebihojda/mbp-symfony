<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;
use Carbon\Carbon;


//Reformat datetime column values using given format (with validation of source value to be valid datetime)

$in = fopen('php://stdin', 'rb');
$out = fopen('php://stdout', 'wb');

$newFormat = '';
if($argc == 2){
    $newFormat = $argv[1];
}else{
    echo 'invalid number of arguments' . PHP_EOL;
    exit(1);
}

$header = fgets($in);
fwrite($out, $header);

while (($text = fgets($in)) !== false) {
    $words = new UnicodeString($text)->trim()->split(',');
    $newWords = new UnicodeString('');
    foreach ($words as $word) {
        try{
            $newDateTime =  Carbon::parse($word->trim()->toString())->format($newFormat);
            $newWords = $newWords->append("$newDateTime,");
        }catch(\Exception $e){
            //echo $e->getMessage().PHP_EOL;
            $newWords = $newWords->append("$word,");
        }
    }
    fwrite($out, $newWords->splice(PHP_EOL, $newWords->length() - 1));
}

fclose($in);
fclose($out);


