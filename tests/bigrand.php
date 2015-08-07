#!/usr/bin/env php
<?php

use Ptilz\Arr;
use Ptilz\BigMath;
use Ptilz\Math;

require __DIR__ . '/../vendor/autoload.php';

/**
 * @param string $limit
 * @return string
 * @see http://stackoverflow.com/a/27371327/65387
 */
function generateRandomLongInt($limit)
{
    $limit = (string) $limit;
    if (!ctype_digit($limit)) {
        throw new \InvalidArgumentException(sprintf('Limit must be numeric string. %s is not.', $limit));
    }
    $limit = ltrim($limit, '0');
    $pseudoint = '';
    $maxLength = strlen($limit);
    $restrict = true;
    for ($i = 0; $i < $maxLength; $i++) {
        $max = $restrict ? $limit[$i] : 9;
        $rand = mt_rand(0, $max);
        if ($restrict && $rand != $limit[$i]) {
            $restrict = false;
        }
        $pseudoint .= $rand;
    }

    $pseudoint = ltrim($pseudoint, '0');
    if ($pseudoint === '') {
        $pseudoint = '0';
    }
    return $pseudoint;
}

$lo = mt_getrandmax()-50;
$hi = mt_getrandmax()+50;
//$step = .01;
//$runs = ($hi-$lo)/$step*1000;
$runs = 100000;
$res = [];

for($i=0; $i<$runs; ++$i) {
    $x = mt_rand($lo, $hi);
    Arr::inc($res,(string)$x);
}

ksort($res,SORT_NUMERIC);
print_r($res);

