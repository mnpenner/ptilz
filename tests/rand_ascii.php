#!/usr/bin/env php
<?php

use Ptilz\Bin;
use Ptilz\BitStream;
use Ptilz\Cli;
use Ptilz\Str;

call_user_func(function() {
    $errorType = [
        E_ERROR              => '<bg:red;fg:white;b> ERROR <reset>',
        E_WARNING            => '<bg:yellow;fg:white;b> WARNING <reset>',
        E_PARSE              => '<bg:red;fg:white;b> PARSING ERROR <reset>',
        E_NOTICE             => '<bg:cyan;fg:white;b> NOTICE <reset>',
        E_CORE_ERROR         => '<bg:red;fg:white;b> CORE ERROR <reset>',
        E_CORE_WARNING       => '<bg:yellow;fg:white;b> CORE WARNING <reset>',
        E_COMPILE_ERROR      => '<bg:red;fg:white;b> COMPILE ERROR <reset>',
        E_COMPILE_WARNING    => '<bg:yellow;fg:white;b> COMPILE WARNING <reset>',
        E_USER_ERROR         => '<bg:red;fg:white;b> USER ERROR <reset>',
        E_USER_WARNING       => '<bg:yellow;fg:white;b> USER WARNING <reset>',
        E_USER_NOTICE        => '<bg:cyan;fg:white;b> USER NOTICE <reset>',
        E_STRICT             => '<bg:light-blue;fg:white;b> STRICT NOTICE <reset>',
        E_RECOVERABLE_ERROR  => '<bg:magenta;fg:white;b> RECOVERABLE ERROR <reset>',
    ];

    $rootLen = strlen(dirname(__DIR__));

    set_error_handler(function($errno, $errstr, $errfile,$errline, $errcontext) use($errorType,$rootLen) {
        if (array_key_exists($errno, $errorType)) {
            $err = $errorType[$errno];
        } else {
            $err = '<bg:magenta;fg:white;b> UNKNOWN ERROR #$errno <reset>';
        }

        $file = substr($errfile,$rootLen);
        echo Cli::colorize("$err $errstr <fg:dark-grey>@</fg> <b>$file</b>:<b>$errline</b>\n");
    });

});



require __DIR__ . '/../vendor/autoload.php';


//for($i=0; $i<10; ++$i) {
//    //echo Str::secureRandomAscii(10,'01') . PHP_EOL;
//    $data = Bin::secureRandomBytes(10);
//    // characters copied from http://base64.sourceforge.net/b64.c   'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
//    echo Str::encode($data,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/')."\n".base64_encode($data).PHP_EOL;
//}
//
//echo PHP_EOL;
//

for($i=0; $i<10; ++$i) {
    $stream = new BitStream(Bin::secureRandomBytes(17),132);
    echo "Z85 ".Str::encode($stream,Str::Z85).PHP_EOL;
    echo "A85 ".Str::encode($stream,Str::ADOBE85).PHP_EOL;
    echo "A96 ".Str::encode($stream,Str::ASCII96).PHP_EOL;
    echo "B64 ".Str::encode($stream,Str::BASE64URL).PHP_EOL;
    echo PHP_EOL;
    //echo Str::secureRandomAscii(128) . PHP_EOL;
    //echo Str::secureRandomAscii(128,$printable) . PHP_EOL;
}

$shortest = new BitStream("\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",132);
echo "Shortest:\n";
echo "Z85 ".Str::encode($shortest,Str::Z85).PHP_EOL;
echo "A85 ".Str::encode($shortest,Str::ADOBE85).PHP_EOL;
echo "A96 ".Str::encode($shortest,Str::ASCII96).PHP_EOL;
echo "B64 ".Str::encode($shortest,Str::BASE64URL).PHP_EOL;
echo PHP_EOL;

$iters = 0;
while(true) {
    ++$iters;

    $bin = Bin::secureRandomBytes(17);
    $stream = new BitStream($bin,132);
    $str = Str::encode($stream,Str::ASCII96);

    if(strlen($str) <= 19) {
        echo number_format($iters).PHP_EOL;
        echo Str::export($bin).PHP_EOL;
        echo $stream.PHP_EOL;
        break;
    }
}

//rMRec<5as5pmYf4qCf{F30
//echo PHP_EOL;
//
//for($i=0; $i<10; ++$i) {
//    echo Str::secureRandomAscii(128) . PHP_EOL;
//}
//
//echo PHP_EOL;
//
//echo Str::encode("Hello World","Helo Wrld"). PHP_EOL;
//echo Str::encode("What does the fox say?","wa-po"). PHP_EOL;
//echo Str::encode("What does the fox say?","jof-tch"). PHP_EOL;

//dump(Bin::isLittleEndian());exit;
//$source = "abcd";
//echo "B64 ".base64_encode($source).PHP_EOL;
//echo "BS  ".Str::encode(new BitStream($source),Str::BASE64).PHP_EOL;

//echo Str::export(implode('',range(chr(0),chr(127)))).PHP_EOL;

//echo PHP_EOL.Str::encode("\xFF",Str::BASE64).PHP_EOL;
//echo PHP_EOL.Str::encode("8mb",Str::BASE64).PHP_EOL;

//echo Str::WHITESPACE;

__halt_compiler();

sGzaFaZ/eRuhM2
nIorKfr9znVGsA==


RDKKTY3Y2Sie2012asuab2
VHOOX]$]#W3/#!"#+=?+,#
