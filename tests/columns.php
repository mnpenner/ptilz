<?php
use Ptilz\Cli;
use Ptilz\Str;

require __DIR__.'/../vendor/autoload.php';



$test = [];
$items = mt_rand(1,100);
for($i=0; $i<$items; ++$i) {
    $str = trim(Str::random(mt_rand(3, 40), "abcdefghijklmnopqrstuvwxyz "));
    if(mt_rand(0,9) === 0) $str = "<info>$str</info>";
    $test[] = $str;
}

Cli::printColumns($test);