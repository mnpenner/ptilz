<?php
use QueryBuilder\QB;

require 'autoload.php';

$qb = QB::from(['t1'=>'my_table'])
    ->leftJoin('x','a','b')
    ->select('a','b','c')
    ->select(['foo'=>'bar'])
    ->addSelect('v')
    ->addSelect('pen','p')
    ->where(['x',1],QB::eq('y',2),['z','>',3],QB::lt('4',4))
    ->addWhere(QB::val('q'),QB::id('x'));

var_dump($qb);
echo $qb.PHP_EOL;