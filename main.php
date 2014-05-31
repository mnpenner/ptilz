<?php
use QueryBuilder\QB;

require 'autoload.php';

$qb = QB::from('t1')
    ->leftJoin('x','a','b')
    ->select('a','b','c');

var_dump($qb);