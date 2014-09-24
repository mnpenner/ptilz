#!/usr/bin/env php
<?php
use Ptilz\Cli;
require __DIR__ . '/../vendor/autoload.php';


for($i=0; $i<32;) {
    for($j=0; $j<32;) {
        Cli::write("<fg:$i;bg:$j>".str_pad("$i;$j",9,' ',STR_PAD_BOTH));
        if(++$j%8===0) Cli::writeLine('<default>');
    }
    ++$i;
}

Cli::writeLine('<default>');

Cli::writeLine("text <b>b</b> text");
Cli::writeLine("text <bold>bold</bold> text");
Cli::writeLine("text <bright>bright</bright> text");
Cli::writeLine("text <dim>dim</dim> text");
Cli::writeLine("text <italic>italic</italic> text");
Cli::writeLine("text <underline>underline</underline> text");
Cli::writeLine("text <blink-slow>blink-slow</blink-slow> text");
Cli::writeLine("text <blink-rapid>blink-rapid</blink-rapid> text");
Cli::writeLine("text <inverse>inverse</inverse> text");
Cli::writeLine("text <negative>negative</negative> text");
Cli::writeLine("text <reverse>reverse</reverse> text");
Cli::writeLine("text <hidden>hidden</hidden> text");
Cli::writeLine("text <conceal>conceal</conceal> text");
Cli::writeLine("text <strike>strike</strike> text");
//Cli::writeLine("text <primary>text</primary> text");
Cli::writeLine("text <fraktur>fraktur</fraktur> text");
Cli::writeLine("text <framed>framed</framed> text");
Cli::writeLine("text <encircled>encircled</encircled> text");
Cli::writeLine("text <overlined>overlined</overlined> text");
Cli::writeLine('<default>');

//case 'black': $colorNumber += 0; break;
//case 'red': $colorNumber += 1; break;
//case 'green': $colorNumber += 2; break;
//case 'yellow': $colorNumber += 3; break;
//case 'blue': $colorNumber += 4; break;
//case 'magenta': $colorNumber += 5; break;
//case 'cyan': $colorNumber += 6; break;
//case 'grey':
//case 'gray':
//case 'light-grey':
//case 'light-gray': $colorNumber += 7; break;
//case 'dark-grey':
//case 'default': $colorNumber += 9; break;
//case 'dark-gray': $colorNumber += 60; break;
//case 'light-red': $colorNumber += 61; break;
//case 'light-green': $colorNumber += 62; break;
//case 'light-yellow': $colorNumber += 63; break;
//case 'light-blue': $colorNumber += 64; break;
//case 'light-magenta': $colorNumber += 65; break;
//case 'light-cyan': $colorNumber += 66; break;
//case 'white': $colorNumber += 67; break;

$colors = [
    'black','red','green','yellow','blue','magenta','cyan','grey',
    'dark-grey','bright-red','bright-green','bright-yellow','bright-blue','bright-magenta','bright-cyan','white'];

foreach($colors as $c) {
    Cli::write("<fg:$c;dim>$c</fg;dim>|");
}
Cli::writeLine('<default>');

foreach($colors as $c) {
    Cli::write("<fg:$c>$c</fg>|");
}
Cli::writeLine('<default>');

foreach($colors as $c) {
    Cli::write("<fg:$c;b>$c</fg;b>|");
}
Cli::writeLine('<default>');

foreach($colors as $c) {
    Cli::write("<fg:$c;highlight>$c</fg;highlight>|");
}
Cli::writeLine('<default>');

foreach($colors as $c) {
    Cli::write("<bg:$c>$c</bg>|");
}
Cli::writeLine('<default>');