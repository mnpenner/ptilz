<?php
namespace Ptilz;

/**
 * Array helper methods
 */
abstract class Arr {
    /**
     * Retrieve a value from an array by key, otherwise a default value.
     *
     * @param array $arr Array
     * @param int|string|array $key Key(s)
     * @param mixed $default Default value if key is not found
     *
     * @return mixed
     */
    public static function get(array $arr, $key, $default = null) {
        if(is_array($key)) {
            foreach($key as $k) {
                if(array_key_exists($k, $arr)) {
                    $arr = $arr[$k];
                } else {
                    return $default;
                }
            }
            return $arr;
        }
        return array_key_exists($key, $arr) ? $arr[$key] : $default;
    }

    /**
     * Increment an array element by some amount. Will not throw a warning if the key is not yet defined.
     *
     * @param array $array
     * @param int|string|array $key
     * @param int $amount
     */
    public static function inc(array &$array, $key, $amount = 1) {
        if(is_array($key)) {
            while(count($key) >= 2) {
                $k = array_shift($key);
                if(!array_key_exists($k,$array)) {
                    $array[$k] = [];
                }
                $array = &$array[$k];
            }
            $key = reset($key);
        }

        if(array_key_exists($key, $array)) {
            $array[$key] += $amount;
        } else {
            $array[$key] = $amount;
        }
    }

    /**
     * Rekey an array with a column of your choice
     *
     * @param array $arr Array to rekey
     * @param string $key Column to use as new key
     * @param bool $unset Remove the key from inside each record
     *
     * @return array Rekeyed array
     */
    public static function rekey(array $arr, $key, $unset = false) {
        $ret = array();
        foreach($arr as $a) {
            $k = $a[$key];
            $ret[$k] = $a;
            if($unset) unset($ret[$k][$key]);
        }
        return $ret;
    }

    /**
     * Extract a single column from an array. Maintains parent key for associative arrays.
     *
     * @param array $array
     * @param int|string $key
     *
     * @return array
     */
    public static function pluck(array $array, $key) {
        if($array === []) return [];
        if(self::isAssoc($array)) {
            $ret = [];
            foreach($array as $k => $v) {
                $ret[$k] = $v[$key];
            }
            return $ret;
        } else {
            return array_column($array, $key);
        }
    }

    /**
     * Reduces an array to elements with the given keys.
     *
     * @param array $array
     * @param array $keys
     * @param bool $reorder Reorder the key/value pairs in array to match that of $keys, and insert null values where keys are missing
     *
     * @return array
     */
    public static function only(array $array, array $keys, $reorder = false) {
        if($reorder) {
            $ret = array();
            foreach($keys as $key) {
                $ret[$key] = self::get($array, $key, null);
            }
            return $ret;
        } else {
            return array_intersect_key($array, array_fill_keys($keys, null));
        }
    }

    /**
     * "Pops" an element out of an array and returns it. `null` key will return the last element.
     *
     * @param array $array
     * @param string $key
     * @return mixed
     */
    public static function pop(array &$array, $key = null) {
        if($key !== null) {
            $ret = $array[$key];
            unset($array[$key]);
            return $ret;
        } else {
            return array_pop($array);
        }
    }

    /**
     * Similar to `array_map(null, $arrays...)` or Python's zip function,
     * returns an array of arrays, taking one value from each array
     * and putting it in the first array, then the second value from each
     * array and putting it into the second array, and so forth. The
     * returned array will be truncated to the length of the shortest
     * array.
     *
     * Example: given [1,2,3] and [a,b,c] it will return [[1,a],[2,b],[3,c]]
     *
     * @return array    Array of arrays
     */
    public static function zip() {
        $result = [];
        $func_args = func_get_args();
        $keys = call_user_func_array('self::keysUnion', $func_args);

        foreach($keys as $i) {
            $result[$i] = [];
            foreach($func_args as $j => $arr) {
                $result[$i][$j] = self::get($arr, $i, null);
            }
        }
        return $result;
    }

    /**
     * Takes an array (usually containing 2-tuples) and turns it into a dictionary (associative array)
     *
     * @param array $arr Array to convert
     * @param int|string $k0 Key that holds keys
     * @param int|string $k1 Key that holds values
     *
     * @return array Dictionary
     */
    public static function dict(array $arr, $k0 = 0, $k1 = 1) {
        $dict = array();
        foreach($arr as $t) {
            if(isset($dict[$t[$k0]])) {
                if(!is_array($dict[$t[$k0]])) {
                    $dict[$t[$k0]] = array($dict[$t[$k0]]);
                }
                $dict[$t[$k0]][] = $t[$k1];
            } else {
                $dict[$t[$k0]] = $t[$k1];
            }
        }
        return $dict;
    }


    /**
     * Regroup an array by a key.
     *
     * See unit test ArrTest::testRegroup for examples.
     *
     * @param array $arr Array to regroup
     * @param array|int|string $keys Array key to group by
     * @param bool $unset Unset the key to remove redundant information
     * @param bool $flatten Flatten the last level so that it is a dictionary rather than a numeric array.
     *
     * @return array    Regrouped array
     */
    public static function regroup(array $arr, $keys, $unset = false, $flatten = false) {
        if(!is_array($keys)) {
            $keys = [$keys];
        }
        $key = array_shift($keys);

        $ret = array();
        foreach($arr as $row) {
            $i = $row[$key];
            if($unset) unset($row[$key]);
            if($flatten && !$keys) {
                $ret[$i] = count($row) === 1 ? reset($row) : $row;
            } else {
                if(!isset($ret[$i])) $ret[$i] = [];
                $ret[$i][] = $row;
            }
        }
        if($keys) {
            foreach($ret as $i => $row) {
                $ret[$i] = self::regroup($row, $keys, $unset, $flatten);
            }
        }
        return $ret;
    }

    /**
     * Concatenates one or more arrays. Values will never be overwritten. Result will have numeric indices.
     *
     * @param array $array1
     * @return mixed
     */
    public static function concat(array $array1) {
        return call_user_func_array('array_merge', array_map('array_values', func_get_args()));
    }

    /**
     * Forces an associative merge, whether or not the array keys are numeric.
     *
     * @param $array1
     * @return mixed
     */
    public static function merge(array $array1) {
        $args = func_get_args();
        $ret = array_shift($args);
        foreach($args as $arr) {
            foreach($arr as $k => $v) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    /**
     * Merges one or more arrays into the first one. The first array will be modified in-place and returned for convenience.
     *
     * @param $array
     * @return mixed
     */
    public static function extend(array &$array) {
        $array = call_user_func_array('array_merge', func_get_args());
        return $array;
    }

    /**
     * Takes keys from one aray and values from another and combines them into a dictionary.
     *
     * @param array $keys
     * @param array $values
     * @return array
     */
    public static function zipdict(array $keys, array $values) {
        $keys = array_intersect_key($keys, $values);
        $out = array();
        foreach($keys as $k => $_) {
            $out[$keys[$k]] = $values[$k];
        }
        return $out;
    }

    /**
     * Removes all elements from an array that do not pass the filter.
     *
     * @param array $input Array to filter
     * @param callable $callback A function with the signature function($value, $key)
     * @return array
     */
    public static function filter(array $input, callable $callback = null) {
        if($input === []) return [];
        if($callback === null) {
            $callback = ['Ptilz\V','isTruthy'];
        }
        $assoc = self::isAssoc($input);
        $ret = [];
        foreach($input as $key => $val) {
            if($callback($val, $key)) {
                if($assoc) $ret[$key] = $val;
                else $ret[] = $val;
            }
        }
        return $ret;
    }


    /**
     * Removes elements from array for which a callback returns true.
     *
     * @param array $input Array to filter
     * @param callable $callback A function with the signature function($value, $key)
     * @return array
     */
    public static function remove(array $input, callable $callback) {
        if($input === []) return [];
        $assoc = self::isAssoc($input);
        $ret = [];
        foreach($input as $key => $val) {
            if(!$callback($val, $key)) {
                if($assoc) $ret[$key] = $val;
                else $ret[] = $val;
            }
        }
        return $ret;
    }

    /**
     * Determines if an array is "associative" (like a dictionary or hash map). True if at least one index is "out of place".
     *
     * @param array $arr
     * @return bool
     */
    public static function isAssoc(array $arr) {
        $i = 0;
        foreach($arr as $k => $v) {
            if($k !== $i) return true;
            ++$i;
        }
        return false;
    }

    /**
     * Determines if an array is "real" -- that is, contains only sequential integer indices starting with 0.
     *
     * @param array $arr
     * @return bool
     */
    public static function isNumeric(array $arr) {
        $i = 0;
        foreach($arr as $k => $v) {
            if($k !== $i) return false;
            ++$i;
        }
        return true;
    }

    /**
     * Returns the value of the first element in the array.
     *
     * @param array $arr
     * @return mixed
     */
    public static function firstValue(array $arr) {
        return reset($arr);
    }

    /**
     * Returns the key of the first element in an array.
     *
     * @param array $arr
     * @return mixed
     */
    public static function firstKey(array $arr) {
        reset($arr);
        return key($arr);
    }

    /**
     * Returns the value of the last element in an array
     *
     * @param array $arr
     * @return mixed
     */
    public static function lastValue(array $arr) {
        return end($arr);
    }

    /**
     * Returns the key of the last element in an array
     *
     * @param array $arr
     * @return mixed
     */
    public static function lastKey(array $arr) {
        end($arr);
        return key($arr);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array $array
     * @return array
     */
    public static function flatten($array) {
        $return = [];

        array_walk_recursive($array, function ($x) use (&$return) {
            $return[] = $x;
        });

        return $return;
    }

    /**
     * Human-friendly implode.
     *
     * @param string[] $pieces List of strings to join together
     * @param string $last Token to insert before the last element when there are 2 or more elements
     * @param string $glue Token to insert between each element when there are 3 or more elements
     * @param bool $serial Use a serial/Oxford/Harvard comma when there are 3 or more elemeents
     * @return string
     */
    public static function readable($pieces, $last = ' and ', $glue = ', ', $serial = false) {
        if(count($pieces) <= 2) return implode($last, $pieces);
        $last_el = array_pop($pieces);
        $ret = implode($glue, $pieces);
        if($serial) $ret .= rtrim($glue);
        return $ret . $last . $last_el;
    }

    /**
     * Reflect a matrix over its main diagonal, switching the rows and columns.
     *
     * @param array $mat
     * @return array|mixed
     */
    public static function transpose(array $mat) {
        $T = [];
        foreach($mat as $m => $row) {
            foreach($row as $n => $val) {
                $T[$n][$m] = $val;
            }
        }
        return $T;
    }

    /**
     * Returns the union of the keys found in each input array.
     *
     * @param array $array1
     * @return array
     */
    public static function keysUnion(array $array1){
        return array_keys(call_user_func_array('self::merge', func_get_args()));
    }

    /**
     * Returns the intersection of the keys found in each input array.
     *
     * @param array $array1
     * @return array
     */
    public static function keysIntersection(array $array1) {
        return array_keys(call_user_func_array('array_intersect_key', func_get_args()));
    }

    /**
     * Applies the callback to the elements of the given arrays
     *
     * @param array|\Traversable $iter
     * @param callable $callback
     * @return array
     */
    public static function map($iter, callable $callback) {
        $outArr = [];
        foreach($iter as $key => $val) {
            $outVal = $callback($val, $key);
            if($outVal instanceof \Generator) {
                foreach($outVal as $ok => $ov) {
                    if(is_int($ok)) {
                        $outArr[] = $ov;
                    } else {
                        $outArr[$ok] = $ov;
                    }
                }
            } else {
                $outArr[$key] = $outVal;
            }
        }
        return $outArr;
    }

    /**
     * Remove a prefix from all keys.
     *
     * @param array $arr
     * @param string $prefix
     * @param bool $unset Unset the element if it does not start with the prefix
     * @return array
     */
    public static function removeKeyPrefix(array $arr, $prefix, $unset = false) {
        $result = [];
        $prefixLen = strlen($prefix);
        foreach($arr as $k => $v) {
            if(Str::startsWith($k, $prefix)) {
                $result[substr($k, $prefixLen)] = $v;
            } elseif(!$unset) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    /**
     * Wraps each element in a before and after string.
     *
     * @param string[]    $arr    Array of strings to wrap
     * @param string      $before Before string
     * @param string      $after  After string
     * @param null|string $glue   String used to join each wrapped element, or null to return an unjoined array
     * @return string|array A string if glue is provided, otherwise an array
     */
    public static function wrap(array $arr, $before, $after, $glue = null) {
        $result = array_map(function ($x) use ($before, $after) {
            return "$before$x$after";
        }, $arr);
        if($glue !== null) return implode($glue, $result);
        return $result;
    }

    public static function repeat($val, $times) {
        return array_fill(0, $times, $val);
    }
}