#!/usr/bin/env php
<?php
use Ptilz\Cli;
require __DIR__ . '/../vendor/autoload.php';

Cli::writeLine("this is <fg:red>red</fg> and this is normal and this is <b>bold</b> and <fg:red>red <b>bold</b> red</fg> norm");
Cli::writeLine("<fg:red>red<fg:light-red>lred<reset>");
Cli::writeLine("<bg:green><fg:red>red on green<reset>");
Cli::writeLine("<fg:blue>blue<reset>");
Cli::writeLine("<fg:blue;b>{}<reset>","BLUE TEXT!");
Cli::writeLine("<fg:9>should be <bg:1>red</fg> and</bg> normal");
Cli::writeLine("&quot; &apos; &#039; <fg:cyan>{0}</fg>",'<fg:green>xxx</fg>');


//echo "\033[0;31mA\033[1;31mA\033[0mA\033[91myyy\033[0mzzz\n";
//echo "\033[91mxxx\033[0m\n";
//echo "\033[1;31mxxx\033[0m\n";

//for($i=0; $i<256;++$i) {
//    for($j=0; $j<256; ++$j) {
//        Cli::write("<fg:$i;bg:$j>$i;$j");
//    }
//}
//Cli::write('<default>');