#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';
$f = \Ptilz\File::nextAvailableFile(__FILE__, '%2$d-%1s%3$s');
echo "created ".$f->path().PHP_EOL;
$f->write("hello\n");
