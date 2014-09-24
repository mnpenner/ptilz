#!/usr/bin/env php
<?php
use Ptilz\Cli;
require __DIR__ . '/../vendor/autoload.php';

//case 'reset': $codes[] = 0; break;
//case 'normal': $codes[] = 0; break;
//case 'default': $codes[] = 0; break;
//case 'b': $codes[] = 1; break;
//case 'bold': $codes[] = 1; break;
//case 'bright': $codes[] = 1; break;
//case 'dim': $codes[] = 2; break;
//case 'italic': $codes[] = 3; break;
//case 'underline': $codes[] = 4; break;
//case 'blink-slow': $codes[] = 5; break;
//case 'blink-rapid': $codes[] = 6; break;
//case 'inverse': $codes[] = 7; break;
//case 'negative': $codes[] = 7; break;
//case 'reverse': $codes[] = 7; break;
//case 'hidden': $codes[] = 8; break;
//case 'conceal': $codes[] = 8; break;
//case 'strike': $codes[] = 9; break;
//case 'primary': $codes[] = 10; break;
//case 'fraktur': $codes[] = 10; break;
//case 'framed': $codes[] = 51; break;
//case 'encircled': $codes[] = 52; break;
//case 'overlined': $codes[] = 53; break;

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


//echo "\033[0;31mA\033[1;31mA\033[0mA\033[91myyy\033[0mzzz\n";
//echo "\033[91mxxx\033[0m\n";
//echo "\033[1;31mxxx\033[0m\n";

for($i=0; $i<32;) {
    for($j=0; $j<32;) {
        Cli::write("<fg:$i;bg:$j>".str_pad("$i;$j",9,' ',STR_PAD_BOTH));
        if(++$j%8===0) Cli::writeLine('<default>');
    }
    ++$i;
}

Cli::writeLine('<default>');