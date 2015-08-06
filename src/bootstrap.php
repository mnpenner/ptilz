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
    $__version__ = explode('.', PHP_VERSION);
    /**
     * The current PHP version as an integer, useful for version comparisons (e.g., int(50207) from version "5.2.7-extra").
     */
    define('PHP_VERSION_ID', ($__version__[0] * 10000 + $__version__[1] * 100 + $__version__[2]));
}

if(!function_exists('intdiv')) {
    /**
     * Integer division
     *
     * @param int $numerator Number to be divide.
     * @param int $divisor   Number which divides the numerator
     * @return int The integer division of numerator by divisor. If divisor is zero, it throws an E_WARNING and returns FALSE. If the numerator is LONG_MIN (-PHP_INT_MAX - 1) and the divisor is -1, it returns zero.
     * @see https://wiki.php.net/rfc/intdiv
     * @see http://php.net/intdiv
     */
    function intdiv($numerator, $divisor) {
        if($divisor == 0) {
            trigger_error("Division by zero", E_USER_WARNING);
            return false;
        }
        if($numerator == -PHP_INT_MAX - 1 && $divisor == -1) return 0;
        return (int)($numerator/$divisor);
    }
}