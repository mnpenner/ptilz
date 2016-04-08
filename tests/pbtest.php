#!/usr/bin/env php
<?php

use Ptilz\ProgressBar;

require __DIR__ . '/../vendor/autoload.php';


$count = mt_rand(10,10000);

echo "Processing $count items...\n";

$pb = new ProgressBar($count);

for($i=0; $i<$count; ++$i) {
    $pb->increment();
    usleep(mt_rand(1e3,1e5));
//    usleep(mt_rand(1e4,2e6));
}

$pb->complete();
//echo "all done!\n";