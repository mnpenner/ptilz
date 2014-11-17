<?php

$prev = $guess = 0.99999999;
$i = 0;

while($guess != 1.0) {
    $prev = $guess;
    $guess = $guess + (1.0 - $guess)/2;
    ++$i;
}

echo number_format($prev,100).PHP_EOL;
echo "$i iterations".PHP_EOL;


$prev = $guess = 0.00000001;
$i = 0;


while($guess != 0.0) {
    $prev = $guess;
    $guess /= 2;
//    echo -$guess.PHP_EOL;
    ++$i;
}

echo $prev.PHP_EOL;
echo "$i iterations".PHP_EOL;