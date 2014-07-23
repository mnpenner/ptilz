<?php
namespace Ptilz;

/**
 * A collection of methods to do with generic objects and variables.
 */
abstract class V {
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

    /**
     * Test if not falsey (according to V::isFalsey)
     * @param mixed $var
     * @return bool
     */
    public static function isTruthy($var) {
        return !self::isFalsey($var);
    }

    /**
     * Test if a variable is false, null, an empty string, integer 0, or an empty array
     * @param mixed$var
     * @return bool
     */
    public static function isFalsey($var) {
        return in_array($var, [false, null, '', 0, []], true);
    }

    /**
     * Returns the first truthy argument (according to V::isTruthy)
     * @return mixed
     */
    public static function coalesce() {
        $args = func_get_args();
        foreach($args as $a) {
            if(self::isTruthy($a)) {
                return $a;
            }
        }
        return null;
    }
}