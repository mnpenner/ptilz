<?php

class Arr {
    /**
     * Retrieve a value from an array by key, otherwise a default value.
     *
     * @param array $arr Array
     * @param int|string $key Key
     * @param mixed $default Default value if key is not found
     *
     * @return mixed
     */
    public static function get(array $arr, $key, $default = null) {
        return array_key_exists($key, $arr) ? $arr[$key] : $default;
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
     * Extract a single column from an array.
     *
     * @param array $array
     * @param int|string $key
     *
     * @return array
     */
    public static function pluck(array $array, $key) {
        $ret = array();
        foreach($array as $k => $v) {
            $ret[$k] = $v[$key];
        }
        return $ret;
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
    public static function keys(array $array, array $keys, $reorder = false) {
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
        $result = array();
        $func_args = func_get_args();
        $args = array_map('array_values', $func_args);
        $min = min(array_map('count', $args));
        for($i = 0; $i < $min; ++$i) {
            $result[$i] = array();
            foreach($args as $j => $arr) {
                $result[$i][$j] = $arr[$i];
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


    public static function regroup(array $arr, $keys, $unset = false) {
        $keys = (array)$keys;
        $key = array_shift($keys);

        $ret = array();
        foreach($arr as $row) {
            $k = $row[$key];
            if(!isset($ret[$k])) $ret[$k] = array();
            if($unset) unset($row[$key]);
            $ret[$k][] = $row;
        }
        if($keys) {
            foreach($ret as $k => $row) {
                $ret[$k] = self::regroup($row, $keys, $unset);
            }
        }
        return $ret;
    }

    /**
     * Concatenates one or more arrays. Values will never be overwritten. Result will have numeric indices.
     *
     * @return mixed
     */
    public static function concat() {
        return call_user_func_array('array_merge', array_map('array_values', func_get_args()));
    }

    /**
     * Merges one or more arrays into the first one. The first array will be modified in-place and returned for convenience.
     *
     * @param $array
     * @return mixed
     */
    public static function extend(&$array) {
        $arrays = array_slice(func_get_args(), 1);
        foreach($arrays as $arr) {
            foreach($arr as $k => $v) {
                $array[$k] = $v;
            }
        }
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
     * Removes elements from array that do not pass the callback.
     *
     * @param array $input
     * @param callable $callback
     * @return array
     */
    public static function remove(array $input, callable $callback) {
        $ret = [];
        foreach($input as $key => $val) {
            if(!$callback($val, $key)) {
                $ret[$key] = $val;
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
     * Returns the first element in an array.
     *
     * @param array $arr
     * @return mixed
     */
    public static function first(array $arr) {
        return reset($array);
    }

    /**
     * Returns the last element in an array
     *
     * @param array $arr
     * @return mixed
     */
    public static function last(array $arr) {
        return end($array);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array $array
     * @return array
     */
    public static function flatten($array) {
        $return = array();

        array_walk_recursive($array, function ($x) use (&$return) {
            $return[] = $x;
        });

        return $return;
    }
}