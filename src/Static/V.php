<?php
namespace Ptilz;
use Ptilz\Exceptions\ArgumentTypeException;

/**
 * A collection of methods to do with generic objects and variables.
 */
abstract class V {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param  mixed $object
     * @return mixed
     */
    public static function with($object) {
        return $object;
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    public static function value($value) {
        return $value instanceof \Closure ? $value() : $value;
    }

    /**
     * Returns the output of var_dump as a string.
     *
     * @param $var
     * @return string
     */
    public static function dump($var) {
        ob_start();
        var_dump($var);
        return rtrim(ob_get_clean(),"\r\n");
    }

    /**
     * Test if not falsey (according to V::isFalsey)
     *
     * @param mixed $var
     * @return bool
     */
    public static function isTruthy($var) {
        return !self::isFalsey($var);
    }

    /**
     * Test if a variable is false, null, an empty string, integer 0, float 0.0, or an empty array. String "0" is *not* considered falsey.
     *
     * @param mixed$var
     * @return bool
     */
    public static function isFalsey($var) {
        return in_array($var, [false, null, '', 0, [], 0.0], true);
    }

    /**
     * Checks if a collection is empty.
     *
     * Numbers and booleans are *not* considered empty.
     *
     * @param mixed $collection
     * @return bool
     */
    public static function isEmpty($collection) {
        if($collection === null || $collection === '' || $collection === []) {
            return true;
        }
        if($collection instanceof \Countable) {
            return count($collection) === 0;
        }
        if($collection instanceof \Traversable) {
            foreach($collection as $_) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Returns the first truthy argument (according to V::isTruthy)
     * @return mixed
     */
    public static function coalesce() {
        $args = func_get_args();
        foreach($args as $a) {
            if(self::isTruthy($a)) {
                return $a;
            }
        }
        return null;
    }

    /**
     * Returns the resource type and number.
     *
     * @param resource $resource
     * @return string
     */
    public static function resourceName($resource) {
        $name = get_resource_type($resource);
        if(preg_match('~(\d+)\z~', (string)$resource, $matches)) {
            $name .= ' #'.$matches[1];
        }
        return $name;
    }

    /**
     * Returns the class name or internal type of a variable.
     *
     * @param mixed $var Variable to check
     * @return string Type
     */
    public static function getType($var) {
        if(is_object($var)) return get_class($var);
        if(is_resource($var)) return self::resourceName($var);
        return gettype($var);
    }

    /**
     * Returns a short, legible representation of a variable.
     *
     * The representation may be lossy; i.e. there may not be enough information to reconstruct the object.
     *
     * @param mixed $var
     * @return string
     */
    public static function toString($var) {
        if(is_string($var)) {
            if(Str::isBinary($var)) {
                if(strlen($var)<=64) return 'b16,'.strtoupper(bin2hex($var));
                return 'b64,'.base64_encode($var);
            }
            return Str::export($var);
        }
        if(is_int($var)) return (string)$var;
        if(is_float($var)) {
            $str = (string)$var;
            if(strpos($str,'.')===false) $str .= '.';
            return $str;
        }
        if(is_array($var)) {
            if(Arr::isNumeric($var)) {
                return '['.implode(',',array_map(__METHOD__,$var)).']';
            }
            $out = [];
            foreach($var as $k=>$v) {
                $out[] = Str::addSlashes($k).':'.self::toString($v);
            }
            return '{'.implode(',',$out).'}';
        }
        if(is_null($var)) return 'null';
        if(is_object($var)) {
            $type = self::getType($var);
            if(method_exists($var,'__debugInfo')) return $type . self::toString($var->__debugInfo());
            if(method_exists($var,'__toString')) return $type . self::toString($var->__toString());
            return $type;
        }
        return self::getType($var);
    }

    /**
     * Export a variable. This returns syntactically valid PHP code.
     *
     * @param mixed $var
     * @return string
     */
    public static function export($var) {
        if(is_string($var)) return Str::export($var);
        $ret = var_export($var, true);
        if(is_float($var) && strpos($ret,'.') === false) {
            $ret .= '.0'; // PHP 7 now adds returns "3.0" for `var_export(3.)` instead of "3"
        }
        return $ret;
    }

    public static function isType($v, $type) {
        switch($type) {
            case 'bool':
            case 'boolean':
                return is_bool($v);
            case 'int':
            case 'integer':
            case 'long':
                return is_int($v);
            case 'float':
            case 'real':
            case 'double':
                return is_float($v);
            case 'null':
                return $v === null;
            case 'true':
                return $v === true;
            case 'false':
                return $v === false;
            case 'resource':
                return is_resource($v);
            case 'string':
                return is_string($v);
            case 'object':
                return is_object($v);
            case 'array':
                return is_array($v);
            // TODO: add support for array<string, string> ? https://docs.hhvm.com/hack/collections/introduction#array-typing
        }
        return is_a($v, $type, true);
    }

    public static function isOneOfType($v, $types) {
        foreach($types as $type) {
            if(self::isType($v, $type)) {
                return true;
            }
        }
        return false;
    }

    public static function assertOneOfType($value, $expectedTypes, $paramName=null) {
        if(!self::isOneOfType($value, $expectedTypes)) {
            throw new ArgumentTypeException($paramName, $expectedTypes);
        }
        // $message = "Argument ";
        // if(strlen($paramName)) {
        //     $message .= "`$paramName` ";
        // }
        // $message .= "was not one of the expected types: ";
        // if(!is_array($expectedTypes)) {
        //     $expectedTypes = explode('|', (string)$expectedTypes);
        // }
        // $message .= ' ' . Arr::readable(array_map(function ($t) {
        //         return "`" . trim($t) . "`";
        //     }, $expectedTypes), ' or ');
        //
        // assert(self::isOneOfType($value, $expectedTypes), $message);
    }
}