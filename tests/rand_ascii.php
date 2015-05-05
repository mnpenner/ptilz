#!/usr/bin/env php
<?php

use Ptilz\Str;

require __DIR__ . '/../vendor/autoload.php';



dump(Str::secureRandomAscii(120,'abcdefghij'));