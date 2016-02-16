<?php
namespace Ptilz;

use Generator;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Exceptions\NotImplementedException;
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
        foreach($trav as $k => $v) {
            yield $exclude_key ? call_user_func($callback, $v) : call_user_func($callback, $v, $k);
        }
    }

    /**
     * Return just the keys from $callback
     */
    const RETURN_KEY = 1;
    /**
     * Return just the values from $callback
     */
    const RETURN_VALUE = 2;
    /**
     * Return both the keys and values from $callback
     */
    const RETURN_BOTH = 3;
    /**
     * Call $callback with value only
     */
    const CALL_VALUE = 4;
    /**
     * Call $callback with key only
     */
    const CALL_KEY = 8;
    /**
     * Call callback with value and key
     */
    const CALL_BOTH = 12;

    /**
     * Filters an iterable to contain just the keys/values that pass the callback.
     *
     * @param array|\Traversable $trav Traversable
     * @param callable|null $callback Function to call for each element
     * @param int $flags Affects what's passed to $callback and what's returned (yielded)
     * @return Generator
     */
    public static function filter($trav, callable $callback=null, $flags=7) {
        if($callback === null) {
            $callback = [V::class,'isTruthy'];
        }

        foreach($trav as $k=>$v) {
            if(Bin::hasFlag($flags, self::CALL_BOTH)) {
                $result = call_user_func($callback, $v, $k);
            } elseif(Bin::hasFlag($flags, self::CALL_VALUE)) {
                $result = call_user_func($callback, $v);
            } elseif(Bin::hasFlag($flags, self::CALL_KEY)) {
                $result = call_user_func($callback, $k);
            } else {
                throw new \BadMethodCallException("Call flag not specified");
            }

            if($result) {
                if(Bin::hasFlag($flags, self::RETURN_BOTH)) {
                    yield $k => $v;
                } elseif(Bin::hasFlag($flags, self::RETURN_VALUE)) {
                    yield $v;
                } elseif(Bin::hasFlag($flags, self::RETURN_KEY)) {
                    yield $k;
                } else {
                    throw new \BadMethodCallException("Return flag not specified");
                }
            }
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
     * Asserts that the argument is foreachable. If not, an exception will be thrown.
     *
     * @param mixed $obj
     * @param null|string $name
     * @throws \Ptilz\Exceptions\ArgumentTypeException
     */
    public static function assert($obj, $name=null) {
        if(!self::isIterable($obj)) {
            // TODO: parse debug_backtrace to get variable name
            throw new ArgumentTypeException($name,['array',Traversable::class]);
        }
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
     * @param bool $include_keys Whether to use the iterator element keys as index.
     * @return array
     */
    public static function toArray($iter, $include_keys=true) {
        return is_array($iter)
            ? ($include_keys ? $iter : array_values($iter))
            : iterator_to_array($iter, $include_keys);
    }

    /**
     * @param array|Traversable $iter
     * @return \ArrayIterator|\IteratorIterator
     */
    public static function toIterator($iter) {
        return is_array($iter) ? new \ArrayIterator($iter) : new \IteratorIterator($iter);
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
     * @param int $a First number
     * @param int $b Second number
     * @param int $max This number will be repeated forever once it's reached
     * @return Generator 0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, 144...
     */
    public static function fibonacci($a=0, $b=1, $max=null) {
        while(true) {
            if($max !== null && $a > $max) {
                break;
            }
            yield $a;
            $tmp = $a + $b;
            $a = $b;
            $b = $tmp;
            unset($tmp);
        }
        unset($a);
        unset($b);
        while(true) {
            yield $max;
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

    /**
     * @param int|float $start
     * @param int|float $end
     * @param int|float $step
     * @return \Generator
     * @throws \Ptilz\Exceptions\NotImplementedException
     */
    public static function range($start, $end, $step = null) {
        throw new NotImplementedException;
    }


    // TODO: wrap all Iterators and Generators in a new IEnumerable class that implements some methods from LINQ: https://msdn.microsoft.com/en-us/library/9eekhta0(v=vs.110).aspx
}