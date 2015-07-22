<?php
namespace Ptilz;

use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionObject;

abstract class Func {
    /**
     * Literally does nothing.
     */
    public static function noop() {}

    /**
     * Returns the number of arguments that a function accepts.
     *
     * @param string|object $class_or_func Class name, class instance, function name or closure
     * @param null|string $method_name     Method name if object or class name was provided
     * @return int
     * @throws \Exception
     */
    public static function arity($class_or_func, $method_name=null) {
        return self::getReflection($class_or_func, $method_name)->getNumberOfParameters();
    }

    /**
     * Returns either an instance of ReflectionFunction or ReflectionMethod.
     *
     * @param string|object $class_or_func Class name, class instance, function name or closure
     * @param null|string $method_name     Method name if object or class name was provided
     * @return ReflectionFunctionAbstract
     * @throws \Exception
     */
    public static function getReflection($class_or_func, $method_name=null) {
        if($method_name !== null) {
            return new ReflectionMethod($class_or_func, $method_name);
        } elseif(is_string($class_or_func)) {
            $p = explode('::',$class_or_func,2);
            if(count($p) === 2) {
                return new ReflectionMethod($p[0], $p[1]);
            } else {
                return new ReflectionFunction($class_or_func);
            }
        } elseif(is_array($class_or_func)) {
            if(array_keys($class_or_func) !== [0,1]) {
                throw new \Exception("Callable arrays must have the format [\$class,\$method]");
            }
            return new ReflectionMethod($class_or_func[0], $class_or_func[1]);
        } else {
            return new ReflectionFunction($class_or_func);
        }
    }

    /**
     * Invokes a method, even if it's inaccessible (private or protected).
     *
     * @param string $class Classname or object (instance of the class) that contains the method.
     * @param string $name  Name of the method.
     * @param mixed $arg0
     * @param mixed $arg1
     * @return mixed
     */
    public static function invokeMethod($class, $name, $arg0 = null, $arg1 = null) {
        $method = new ReflectionMethod($class, $name);
        $method->setAccessible(true);
        return $method->invokeArgs(is_object($class) ? $class : null, array_slice(func_get_args(), 2));
    }

    /**
     * Invokes a method, even if it's inaccessible (private or protected).
     *
     * @param string $class Classname or object (instance of the class) that contains the method.
     * @param string $name  Name of the method.
     * @param array $args
     * @return mixed
     */
    public static function invokeMethodArgs($class, $name, $args = []) {
        $method = new ReflectionMethod($class, $name);
        $method->setAccessible(true);
        return $method->invokeArgs(is_object($class) ? $class : null, $args);
    }
}