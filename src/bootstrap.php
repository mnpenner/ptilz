<?php

if(!defined('PHP_INT_MIN')) {
    /**
     * The smallest integer supported in this build of PHP. Usually int(-2147483648) in 32 bit systems and int(-9223372036854775808) in 64 bit systems.
     */
    define('PHP_INT_MIN', ~PHP_INT_MAX);
}

if(!function_exists('with')) {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    function with($object) {
        return $object;
    }
}


if(!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value) {
        return $value instanceof Closure ? $value() : $value;
    }
}

if(!defined('PHP_VERSION_ID')) {
    $_version_iF8dFt6W = explode('.', PHP_VERSION);
    /**
     * The current PHP version as an integer, useful for version comparisons (e.g., int(50207) from version "5.2.7-extra").
     */
    define('PHP_VERSION_ID', ($_version_iF8dFt6W[0] * 10000 + $_version_iF8dFt6W[1] * 100 + $_version_iF8dFt6W[2]));
    unset($_version_iF8dFt6W);
}

if(!function_exists('intdiv')) {
    /**
     * Integer division
     *
     * @param int $numerator Number to be divide.
     * @param int $divisor   Number which divides the numerator
     * @return int The integer division of numerator by divisor. If divisor is zero, it throws an E_WARNING and returns FALSE.
     * @see https://wiki.php.net/rfc/intdiv
     * @see http://php.net/intdiv
     */
    function intdiv($numerator, $divisor) {
        if($divisor == 0) {
            trigger_error("Division by zero", E_USER_WARNING);
            return false;
        }
        return (int)($numerator/$divisor);
    }
}

if(!interface_exists('Throwable')) {
    /**
     * Throwable is the base interface for any object that can be thrown via a throw statement in PHP 7,
     * including Error and Exception.
     * @link http://php.net/manual/en/class.throwable.php
     * @since 7.0
     */
    class Throwable extends \Exception {}
}