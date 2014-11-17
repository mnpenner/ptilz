#!/usr/bin/env php
<?php

use Ptilz\Arr;
use Ptilz\BigMath;
use Ptilz\Math;

require __DIR__ . '/../vendor/autoload.php';

//echo Math::ln(5,25).PHP_EOL;
//echo Math::log(100).PHP_EOL;
//echo Math::randInt(1000,PHP_INT_MAX).PHP_EOL;
//echo '0.698970004336018804786261105275506973231810118537891458689572...';


$lo = 0;
$hi = .1;
$step = .01;
//$runs = ($hi-$lo)/$step*1000;
$runs = 110000;
$res = [];

for($i=0; $i<$runs; ++$i) {
    $x = Math::rand($lo, $hi, $step, true);
    Arr::inc($res,(string)$x);
}

ksort($res,SORT_NUMERIC);
print_r($res);




$runs = 100000;
$res = [];

for($i=0; $i<$runs; ++$i) {
    $x = Math::rand($lo, $hi, $step, false);
    Arr::inc($res,(string)$x);
}

ksort($res,SORT_NUMERIC);
print_r($res);