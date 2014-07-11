<?php
namespace Ptilz;

/**
 * A collection of methods to do with generic objects and variables.
 */
abstract class Obj {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    public static function with($object) {
        return $object;
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    public static function value($value) {
        return $value instanceof \Closure ? $value() : $value;
    }

    /**
     * Returns the output of var_dump as a string.
     *
     * @param $var
     * @return string
     */
    public static function dump($var) {
        ob_start();
        var_dump($var);
        return rtrim(ob_get_clean(),"\r\n");
    }

    public static function isTruthy($var) {
        return !self::isFalsey($var);
    }

    public static function isFalsey($var) {
        return in_array($var, [false, null, '', 0, []], true);
    }
}