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
            yield $exclude_key ? call_user_func($callback, $v1) : call_user_func($callback, $v1, $k1);
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
     * Checks if an object can be used with the count() function.
     *
     * @param mixed $obj
     * @return bool
     */
    public static function isCountable($obj) {
        return is_array($obj) || $obj instanceof \Countable;
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

    /**
     * Generates numbers from the Fibonacci sequence
     *
     * @return \Generator 0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, 144...
     */
    public static function fibonacci() {
        $a = 0;
        $b = 1;
        while(true) {
            yield $a;
            $tmp = $a + $b;
            $a = $b;
            $b = $tmp;
            unset($tmp);
        }
    }

    /**
     * Returns a specified number of contiguous elements from the start of a sequence.
     *
     * @param \Traversable $iter The sequence to return elements from.
     * @param int $count The number of elements to return.
     * @return \Generator
     */
    public static function take($iter, $count) {
        foreach($iter as $k=>$v) {
            if($count <= 0) return;
            yield $k => $v;
            --$count;
        }
    }

    /**
     * Bypasses a specified number of elements in a sequence and then returns the remaining elements.
     *
     * @param \Traversable $iter The sequence on which to advance.
     * @param int $count The number of elements to skip before returning the remaining elements.
     * @return \Iterator
     */
    public static function skip($iter, $count) {
        foreach($iter as $k=>$v) {
            if($count > 0) {
                --$count;
            } else {
                yield $k => $v;
            }
        }
    }

    // TODO: wrap all Iterators and Generators in a new IEnumerable class that implements some methods from LINQ: https://msdn.microsoft.com/en-us/library/9eekhta0(v=vs.110).aspx
}