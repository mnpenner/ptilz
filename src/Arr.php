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
     * @param string $type "bool"|"int"|"float"|"array"|"object"|"null" Cast return value to this type or return $default on failure
     *
     * @return mixed
     */
    public static function get(array $arr, $key, $default = null, $type=null) {
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
        if(array_key_exists($key, $arr)) {
            $result = $arr[$key];
            if($type) {
                if(!settype($result, $type)) {
                    return $default;
                }
            }
            return $result;
        }
        return $default;
    }

    /**
     * Retrieve a value from an array by key, allowing PHP's square[bracket][notation].
     *
     * @param array $arr Array
     * @param string $key Key to fetch. May use PHP's square[bracket][notation].
     * @param mixed $default Default value if key is not found
     *
     * @return mixed
     */
    public static function getDeep(array $arr, $key, $default = null) {
        // todo: merge with above??
        $name = strtok($key, '[');
        if(!isset($arr[$name])) return $default;
        $ret = &$arr[$name];
        $rem = strtok(null);
        if($rem) {
            $rem = explode('[', str_replace(']', '', $rem));
            while(($k = array_shift($rem)) !== null) {
                if(!isset($ret[$k])) {
                    if($k === '') { // empty brackets[] -- TODO: stop and return result as an array with any remaining keys passed to pluck?
                        continue;
                    }
                    return $default;
                }
                $ret = &$ret[$k];
            }
        }
        return $ret;
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
            for($p=reset($key),$n=next($key); $n!==false; $p=$n,$n=next($key)) {
                if(!array_key_exists($p, $array)) {
                    $array[$p] = [];
                }
                $array = &$array[$p];
            }
            $key = $p;
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
        // TODO: consider rename to "keyBy" and allow callable for $key, like \Illuminate\Support\Collection::keyBy
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
     * If the key is not found or the array is empty, return $default instead.
     *
     * @param array $array
     * @param string|int|null $key
     * @param null $default
     * @return mixed
     */
    public static function pop(array &$array, $key = null, $default=null) {
        if($key !== null) {
            if(array_key_exists($key, $array)) {
                $ret = $array[$key];
                unset($array[$key]);
                return $ret;
            }
        } elseif(count($array)) {
            return array_pop($array);
        }
        return $default;
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
     * @param int|string $k0 Key that holds keys. Defaults to first key of first sub-array.
     * @param int|string $k1 Key that holds values. Defaults to second key of first sub-array.
     *
     * @return array Dictionary
     */
    public static function dict(array $arr, $k0 = 0, $k1 = 1) {
        if(!$arr) return [];
        if(func_num_args()===1) list($k0,$k1) = array_keys(reset($arr));
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
        // TODO: rename to groupBy? \Illuminate\Support\Collection::groupBy
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
     * Merges the first level of the arrays associatively. Deep numeric arrays will be concatenated.
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public static function mergeRecursive(array $arr1, array $arr2) {
        // fixme: should this replace either merge or extend?
        // todo: allow many arrays. check against $merged instead of $arr1
        $merged = $arr1;
        foreach($arr2 as $k=>$v) {
            if(!isset($arr1[$k]) || !is_array($arr1[$k]) || !is_array($v)) {
                $merged[$k] = $v;
            } elseif(self::isNumeric($v) && self::isNumeric($arr1[$k])) {
                $merged[$k] = array_merge($arr1[$k], $v);
            } else {
                $merged[$k] = self::mergeRecursive($arr1[$k], $v);
            }
        }
        return $merged;
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
     * @deprecated Use the built-in function array_combine
     */
    public static function zipdict(array $keys, array $values) {
        return array_combine($keys, $values);
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
            $callback = [V::class,'isTruthy'];
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
     * @param array $arr Array to test
     * @param bool $quick Perform a quick test (does not check every single key)
     * @return bool
     */
    public static function isAssoc(array $arr, $quick=false) {
        if($quick && count($arr) > 10) {
            $i = 0;
            foreach($arr as $k => $v) {
                if($k !== $i) return true;
                if($i >= 4) break;
                ++$i;
            }

            $i = count($arr) - 1;
            $end = $i - 4;
            for(end($arr),$k=key($arr); $k!==null; prev($arr),$k=key($arr)){
                if($k !== $i) return true;
                if($k <= $end) break;
                --$i;
            }
        } else {
            $i = 0;
            foreach($arr as $k => $v) {
                if($k !== $i) return true;
                ++$i;
            }
        }
        return false;
    }

    /**
     * Determines if an array is "real" -- that is, contains only sequential integer indices starting with 0.
     *
     * @param array $arr Array to test
     * @param bool $quick Perform a quick test (does not check every single key)
     * @return bool
     */
    public static function isNumeric(array $arr, $quick=false) {
        return !static::isAssoc($arr, $quick);
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
     * Applies a callback to the elements of the given array
     *
     * If the callback is a generator, each yielded value will be merged
     * into the resulting array. This allows you to re-key the array
     * or omit elements completely.
     *
     * @param array|\Traversable $iter
     * @param callable $callback function($value, $key)
     * @param bool $exclude_key Don't pass the array key as the 2nd param to the callback function
     * @return array
     */
    public static function map($iter, callable $callback, $exclude_key=false) {
        $outArr = [];
        foreach($iter as $key => $val) {
            $outVal = $exclude_key ? $callback($val) : $callback($val, $key);
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
        // fixme: rename to "surround" like in _.string?
        $result = array_map(function ($x) use ($before, $after) {
            return "$before$x$after";
        }, $arr);
        if($glue !== null) return implode($glue, $result);
        return $result;
    }

    /**
     * Returns an array with the same value repeated the specified number of times.
     *
     * @param mixed $val Value to repeat
     * @param int $times Number of times to repeat; resulting array length
     * @return array
     */
    public static function repeat($val, $times) {
        return array_fill(0, $times, $val);
    }

    /**
     * Shuffle an array, maintaining keys.
     *
     * @param array $array
     * @param bool $maintainIndexes
     * @return array
     */
    public static function shuffle(array $array, $maintainIndexes = true) {
        if(count($array) <= 1) {
            return $array;
        }
        $len = count($array);
        $rand = str_split(Bin::secureRandomBytes($len * 16), 16);
        if($maintainIndexes) {
            $keys = array_keys($array);
            array_multisort($rand, SORT_STRING, $keys, $array);
            return array_combine($keys, $array);
        } else {
            array_multisort($rand, SORT_STRING, $array);
            return $array;
        }
    }

    /**
     * Return a random key from an array
     *
     * @param array $array
     * @return int|string
     */
    public static function randomKey(array $array) {
        $keys = array_keys($array);
        $idx = mt_rand(0, count($keys) - 1);
        return $keys[$idx];
    }

    /**
     * Return a random value from an array.
     *
     * @param array $array
     * @return mixed
     */
    public static function randomValue(array $array) {
        return $array[self::randomKey($array)];
    }

    /**
     * Returns random elements from an array
     *
     * @param array $array
     * @param int $count Number of elements to return
     * @return array
     */
    public static function randomSubset(array $array, $count) {
        $shuf = self::shuffle($array);
        return array_slice($shuf, 0, $count);
    }


    /**
     * Join an array into a human readable sentence.
     *
     * @param string[] $array
     * @param string $delimiter
     * @param string $lastDelimiter
     * @param bool $serial_comma
     * @return string
     * @deprecated Use \Ptilz\Arr::readable
     */
    public static function toSentence($array, $delimiter = ', ', $lastDelimiter = ' and ', $serial_comma=false) {
        return self::readable($array, $lastDelimiter, $delimiter, $serial_comma);
    }

    /**
     * Push an element into a sub-array.
     *
     * @param array $array
     * @param string|array $key
     * @param mixed $var
     */
    public static function push(&$array, $key, $var=null) {
        if(func_num_args() === 3) {
            if(is_array($key)) {
                while(count($key) >= 2) {
                    $k = array_shift($key);
                    if(!array_key_exists($k, $array)) {
                        $array[$k] = [];
                    }
                    $array = &$array[$k];
                }
                $key = reset($key);
            }
            if(array_key_exists($key, $array)) {
                $array[$key][] = $var;
            } else {
                $array[$key] = [$var];
            }
        } else {
            $array[] = $key;
        }
    }

    /**
     * Searches for a value in sorted array. If found, returns the index of the match.
     * If not found, return value will be less than zero. Specifically, it will contain
     * the bitwise complement of the index of the next element that is larger than
     * the target value, i.e. the index where the value should be inserted to maintain order.
     * 
     * @param array $array Array to search
     * @param mixed $target Target value to search for
     * @param null|callable $cmp Compare function, like `strcmp`
     * @return int
     */
    public static function binarySearch(array $array, $target, $cmp = null) {
        if($cmp === null) {
            $cmp = V::class . '::compare';
        }
        $L = 0;
        $R = count($array) - 1;
        while($L <= $R) {
            $m = (int)(($L + $R) / 2);
            $c = $cmp($array[$m], $target);
            if($c < 0) {
                $L = $m + 1;
            } elseif($c > 0) {
                $R = $m - 1;
            } else {
                return $m;
            }
        }
        return ~($L > $R ? $L : $m);
    }
}