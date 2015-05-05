<?php

if(!defined('PHP_INT_MIN')) {
    /** The smallest integer supported in this build of PHP. */
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
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}