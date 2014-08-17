<?php
namespace Ptilz;

use Generator;
use Traversable;

class Iter {
    /**
     * Applies a callback to the given traversable as it is iterated.
     *
     * @param array|Traversable $trav
     * @param callable $callback
     * @return Generator
     */
    public static function map($trav, callable $callback) {
        foreach($trav as $k1 => $v1) {
            yield $callback($v1, $k1);
        }
    }

    /**
     * Checks if an object is foreachable.
     *
     * @param mixed $obj
     * @return bool
     */
    public static function isIterable($obj) {
        return is_array($obj) || $obj instanceof Traversable;
    }


    /**
     * @param array|Traversable $iter
     * @return array
     */
    public static function toArray($iter) {
        $result = [];
        foreach($iter as $k=>$v) {
            $result[$k] = $v;
        }
        return $result;
    }

    /**
     * Returns true if every value passes the callback
     *
     * @param array|Traversable $trav
     * @param callable $callback Defaults to V::isTruthy
     * @return bool
     */
    public static function all($trav, callable $callback=null) {
        if($callback === null) $callback = ['Ptilz\V','isTruthy'];
        foreach($trav as $v) {
            if(!$callback($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns true if any value passes the callback
     *
     * @param array|Traversable $trav
     * @param callable $callback Defaults to V::isTruthy
     * @return bool
     */
    public static function any($trav, callable $callback=null) {
        if($callback === null) $callback = ['Ptilz\V','isTruthy'];
        foreach($trav as $v) {
            if($callback($v)) {
                return true;
            }
        }
        return false;
    }
}