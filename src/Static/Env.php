<?php
namespace Ptilz;

abstract class Env {
    /**
     * Tests if PHP is running in a command-line environment.
     *
     * @return bool
     */
    public static function isCli() {
        return php_sapi_name() === 'cli';
    }

    /**
     * @return string
     * @deprecated Use username
     */
    public static function posixUserName() {
        return posix_getpwuid(posix_geteuid())['name'];
    }

    /**
     * Gets the username of the user running the process.
     *
     * @return string
     */
    public static function username() {
        if(function_exists('posix_getlogin')) {
            $login = posix_getlogin();
            if($login !== false) {
                return $login;
            }
        }
        return getenv('USER') ?: getenv('username');
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

    /**
     * Checks if your PHP version is equal to or greater than the given version.
     *
     * @param string $ver_str
     * @return bool
     */
    public static function PhpVerAtLeast($ver_str) {
        return version_compare(PHP_VERSION, $ver_str, '>=');
    }
}