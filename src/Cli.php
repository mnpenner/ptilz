<?php
namespace Ptilz;

/**
 * Command-line methods
 */
abstract class Cli {

    public static function write($format) {
        echo call_user_func_array(['Str', 'format'], func_get_args());
    }

    public static function writeLine($format) {
        echo call_user_func_array(['Str', 'format'], func_get_args()) . PHP_EOL;
    }
}