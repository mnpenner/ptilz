<?php
namespace Ptilz;

abstract class Env {
    public static function isCli() {
        return php_sapi_name() === 'cli';
    }

    public static function posixUserName() {
        return posix_getpwuid(posix_geteuid())['name'];
    }
}