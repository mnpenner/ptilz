#!/usr/bin/env php
<?php

use Ptilz\Cli;
use Ptilz\Str;

call_user_func(function() {
    $errorType = array (
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
    );

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



dump(Str::secureRandomAscii(120,'abcdefghij'));