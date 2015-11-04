#!/usr/bin/env php
<?php
use Ptilz\Shell;
require __DIR__ . '/../vendor/autoload.php';

var_dump(Shell::status('echo',['foo']));
var_dump(Shell::status('echo',['bar'],$stdout));
var_dump($stdout);
var_dump(Shell::status('(Y!@#fwja0faewr',[],$stdout,$stderr));
var_dump($stdout);
var_dump($stderr);