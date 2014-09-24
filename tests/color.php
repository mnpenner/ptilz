#!/usr/bin/env php
<?php
use Ptilz\Cli;
require __DIR__ . '/../vendor/autoload.php';


Cli::writeLine("this is <fg:red>red</fg> and this is normal and this is <b>bold</b> and <fg:red>red <b>bold</b> red</fg> norm");
Cli::writeLine("<fg:red>red<fg:lred>lred<reset>");
Cli::writeLine("<bg:green><fg:red>red on green<reset>");

echo "\033[0;31mA\033[1;31mA\033[0mA\033[91myyy\033[0mzzz\n";
echo "\033[91mxxx\033[0m\n";
echo "\033[1;31mxxx\033[0m\n";