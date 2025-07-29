<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\String\UnicodeString;

//Truncate string column values to given length (with validation that the value is a string)

$in = fopen('php://stdin', 'rb');
$out = fopen('php://stdout', 'wb');

$text = fgets($in);
$headers = new UnicodeString($text)->trim()->split(',');

$maxLength = 0;
if($argc == 2){
    if(is_numeric($argv[1]) && $argv[1] >= 0){
        $maxLength = (int)$argv[1];
    }else{
        echo 'invalid numeric argument' . PHP_EOL;
        exit(1);
    }
}else{
    echo 'invalid number of arguments' . PHP_EOL;
    exit(1);
}

fwrite($out, $text);

while (($text = fgets($in)) !== false) {
    $words = new UnicodeString($text)->trim()->split(',');
    $newWords = new UnicodeString('');
    foreach ($words as $word) {
        if(!is_numeric($word->toString()) && $word->length() >= $maxLength){
            $newWord = $word->slice($word->length() - $maxLength, $maxLength);
            $newWords = $newWords->append("$newWord,");
        }else{
            $newWords = $newWords->append("$word,");
        }
    }
    fwrite($out, $newWords->splice(PHP_EOL, $newWords->length() - 1));
}

fclose($in);
fclose($out);