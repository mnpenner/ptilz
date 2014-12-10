<?php
namespace Ptilz;

use ReflectionFunction;
use ReflectionObject;

abstract class Func {
    /**
     * Literally does nothing.
     */
    public static function noop() {}

    /**
     * Returns the number of arguments that a function accepts.
     *
     * @param object|callable $lambda
     * @return int
     */
    public static function arity($lambda) {
        $m = new ReflectionFunction($lambda);
        return $m->getNumberOfParameters();
    }

    /**
     * Invokes a method, even if it's inaccessible (private or protected).
     *
     * @param string $class Classname or object (instance of the class) that contains the method.
     * @param string $name Name of the method.
     * @param mixed $arg0
     * @param mixed $arg1
     */
    public static function invokeMethod($class, $name, $arg0 = null, $arg1 = null) {
        $method = new \ReflectionMethod($class, $name);
        $method->setAccessible(true);
        $method->invokeArgs(is_object($class) ? $class : null, array_slice(func_get_args(), 2));
    }

    /**
     * Invokes a method, even if it's inaccessible (private or protected).
     *
     * @param string $class Classname or object (instance of the class) that contains the method.
     * @param string $name Name of the method.
     * @param array $args
     */
    public static function invokeMethodArgs($class, $name, $args = []) {
        $method = new \ReflectionMethod($class, $name);
        $method->setAccessible(true);
        $method->invokeArgs(is_object($class) ? $class : null, $args);
    }
}