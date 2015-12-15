<?php
use Ptilz\Str;
use Ptilz\Bin;

require __DIR__ . '/../vendor/autoload.php';

function encodeLen($alpha, $src_bits=128) {
    $alpha_bits = log($alpha,2);
    return [ceil($src_bits/ceil($alpha_bits)),ceil($src_bits/floor($alpha_bits))];
}


for($i=2; $i<256; ++$i) {
    $x = encodeLen($i);
    if($x[0] == $x[1]) {
        echo "$i $x[0]\n";
    }
}

for($i=0; $i<100; ++$i) {
    $data = Bin::secureRandomBytes(16);
    $data[0] = chr(Bin::setBit(ord($data), 7, 0));
    echo Str::encode($data,Str::BASE64URL).PHP_EOL;
    $data[0] = chr(Bin::setBit(ord($data), 7, 1));
    echo Str::encode($data,Str::BASE64URL).PHP_EOL;
    echo PHP_EOL;
}

//for($i=0; ; ++$i) {
//    $y = Str::encode(Bin::secureRandomBytes(16),Str::BASE64URL);
//    if(strlen($y) !== 22) {
//        throw new \Exception($y);
//    }
//}