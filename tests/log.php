#!/usr/bin/env php
<?php

use Ptilz\BigMath;
use Ptilz\Math;

require __DIR__ . '/../vendor/autoload.php';

//echo Math::ln(5,25).PHP_EOL;
echo BigMath::ln(100) .PHP_EOL;
//echo Math::randInt(1000,PHP_INT_MAX).PHP_EOL;
//echo '0.698970004336018804786261105275506973231810118537891458689572...';


//$buckets = 10;
//$lo = mt_rand(0,10000);
//$hi = $lo + $buckets;
//$runs = $buckets*1000;
//$res = array_fill($lo,$buckets,0);
//
//for($i=0; $i<$runs; ++$i) {
//    $x = Math::randInt($lo,$hi);
//    ++$res[$x];
//}
//
//print_r($res);