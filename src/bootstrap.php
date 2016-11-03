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
     * @param int $dividend Number to be divided.
     * @param int $divisor Number which divides the dividend.
     * @return int The integer quotient of the division of dividend by divisor.
     * @throws ArithmeticError
     * @throws DivisionByZeroError
     * @see https://wiki.php.net/rfc/intdiv
     * @see http://php.net/intdiv
     */
    function intdiv($dividend, $divisor) {
        $dividend = (int)$dividend;
        $divisor = (int)$divisor;
        if($divisor === 0) {
            throw new DivisionByZeroError("Division by zero");
        }
        if($dividend === PHP_INT_MIN && $divisor === -1) {
            throw new ArithmeticError("Division of PHP_INT_MIN by -1 is not an integer");
        }
        return (int)($dividend/$divisor);
    }
}

if(!class_exists('Error')) {
    /**
     * Error is the base class for all internal PHP errors.
     */
    class Error extends \Exception {}
}

if(!class_exists('ArithmeticError')) {
    /**
     * ArithmeticError is thrown when an error occurs while performing mathematical operations. In PHP 7.0, these errors include attempting to perform a bitshift by a negative amount, and any call to intdiv() that would result in a value outside the possible bounds of an integer.
     */
    class ArithmeticError extends \Error {}
}

if(!class_exists('DivisionByZeroError')) {
    /**
     * DivisionByZeroError is thrown when an attempt is made to divide a number by zero.
     */
    class DivisionByZeroError extends \ArithmeticError {}
}

if(!function_exists('swap')) {
    function swap(&$a, &$b) {
        $t = $a;
        $a = $b;
        $b = $t;
    }
}