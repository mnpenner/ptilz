#!/usr/bin/env php
<?php

use Ptilz\ProgressBar;

require __DIR__ . '/../vendor/autoload.php';


$count = mt_rand(16,30);

$pb = new ProgressBar($count);

for($i=0; $i<$count; ++$i) {
    $pb->increment();
    sleep(1);
}

$pb->complete();
echo "all done!\n";