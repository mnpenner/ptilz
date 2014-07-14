#!/usr/bin/env php
<?php

use Ptilz\File;

require __DIR__.'/../vendor/autoload.php';

$tmp = File::temporary();

var_dump($tmp->path());
var_dump($tmp->write('abc'));
//$tmp->flush();
$tmp->seek(0);
var_dump($tmp->read(3));



$uniq = File::createUnique('.','txt');

var_dump($uniq->path());
var_dump($uniq->write('abc'));
//$uniq->flush();
//$uniq->seek(0);
//var_dump($uniq->read(3));


$self = File::openRead(__FILE__);
foreach($self->lines() as $line) {
    echo $line.PHP_EOL;
}