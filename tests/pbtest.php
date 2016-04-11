#!/usr/bin/env php
<?php

use Ptilz\ProgressBar;

require __DIR__ . '/../vendor/autoload.php';


//$count = mt_rand(10,100000);
$count = 1000;

echo "Processing ".number_format($count)." items...\n";

$pb = new ProgressBar($count);

for($i=0; $i<$count; ++$i) {
    $pb->increment();
    usleep(33333);
//    usleep(mt_rand(1e4,2e6));
}

$pb->complete();
//echo "all done!\n";