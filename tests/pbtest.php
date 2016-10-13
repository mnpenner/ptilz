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
    if($i === 200) {
        $pb->writeLine("hello $i");
    }
    if($i === 400) {
        $pb->writeLine("world $i\n");
    }
    if($i === 600) {
        $pb->writeLine("foo $i\r\n\r\n");
    }
    if($i === 800) {
        $pb->writeLine("foo".PHP_EOL."bar");
    }
//    usleep(mt_rand(1e4,2e6));
}

$pb->complete();
//echo "all done!\n";