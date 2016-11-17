#!/usr/bin/env php
<?php

use Ptilz\Arr;

require __DIR__ . '/../vendor/autoload.php';


$len = 5;
$counts = [];
$src = ['a','b','c','d','e'];
$iters = $len*10000;

for($i=0; $i<$iters; ++$i) {
    $shuffled = Arr::shuffle($src);
    // dump($shuffled);
    $j=0;
    foreach($shuffled as $x) {
        @++$counts[$j][$x];
        ++$j;
    }
}

dump($counts);