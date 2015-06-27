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
     * @param bool $exclude_key Don't pass the array key as the 2nd param to the callback function
     * @return Generator
     */
    public static function map($trav, callable $callback, $exclude_key=false) {
        foreach($trav as $k1 => $v1) {
            yield $exclude_key ? $callback($v1) : $callback($v1, $k1);
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
     * Copy the iterator into an array
     *
     * @param array|Traversable $iter The iterator being copied.
     * @param bool $use_keys Whether to use the iterator element keys as index.
     * @return array
     */
    public static function toArray($iter, $use_keys=true) {
        return is_array($iter) ? $iter : iterator_to_array($iter, $use_keys);
    }

    /**
     * Returns true if every value passes the callback
     *
     * @param array|Traversable $trav
     * @param callable $callback Defaults to V::isTruthy
     * @return bool
     */
    public static function all($trav, callable $callback=null) {
        if($callback === null) $callback = [V::class,'isTruthy'];
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
        if($callback === null) $callback = [V::class,'isTruthy'];
        foreach($trav as $v) {
            if($callback($v)) {
                return true;
            }
        }
        return false;
    }
}