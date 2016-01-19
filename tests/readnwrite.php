#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

echo "what's your name?\n";
$name = rtrim(fgets(STDIN, 4096));
echo "hello {$name}xx\n";