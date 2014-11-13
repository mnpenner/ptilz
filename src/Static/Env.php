<?php
namespace Ptilz;

abstract class Env {
    public static function isCli() {
        return php_sapi_name() === 'cli';
    }

    public static function posixUserName() {
        return posix_getpwuid(posix_geteuid())['name'];
    }

    /**
     * Determines if the host operating system is Windows.
     *
     * @return bool
     * @credit http://stackoverflow.com/a/14708374/65387
     */
    public static function isWindows() {
        // this is not completely reliable; someone *could* define this constant themselves
        // other usable constants from http://ca2.php.net/manual/en/function.php-uname.php
        // http://php.net/manual/en/info.constants.php
        // DIRECTORY_SEPARATOR, PHP_SHLIB_SUFFIX, PATH_SEPARATOR
        return defined('PHP_WINDOWS_VERSION_MAJOR');
    }
}