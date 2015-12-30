#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Ptilz\Iter;


$q = 'xyz';
Iter::assert($q);


__halt_compiler();

array:1 [
  0 => array:6 [
    "file" => "D:\Websites\ptilz\tests\assert_iter.php"
    "line" => 10
    "function" => "assert"
    "class" => "Ptilz\Iter"
    "type" => "::"
    "args" => array:1 [
      0 => & "xyz"
    ]
  ]
]
