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
}