<?php

class Json {
    /**
     * JSON-encodes a value. Escaping can be prevented on a sub-element via Json::literal.
     *
     * @param mixed $var The value being encoded. Can be any type except a resource.
     * @param int $options Options passed to `json_encode`. Everything except `JSON_PRETTY_PRINT` should work.
     * @throws JsonException
     * @return string
     * @see http://us3.php.net/manual/en/json.constants.php
     */
    public static function encode($var, $options=0) {
        if(is_scalar($var)) {
            return json_encode($var, $options);
        }
        if(is_array($var)) {
            if(self::is_assoc($var)) {
                $bits = array();
                foreach($var as $k => $v) {
                    $bits[] = json_encode($k, $options) . ':' . self::encode($v);
                }
                return '{' . implode(',', $bits) . '}';
            } else {
                return '[' . implode(',',array_map(array('self', __FUNCTION__), $var)) . ']';
            }
        }
        if(is_object($var)) {
            if($var instanceof _JsLiteral) {
                return $var->str;
            }
            if($var instanceof JsonSerializable) {
                return json_encode($var->jsonSerialize());
            }
            return json_encode($var, $options);
        }
        throw new JsonException('Could not json encode variable of type '.gettype($var));
    }

    /**
     * Returns a "literal" or "raw" value which will not be escaped by Json::encode
     *
     * @param string $str Raw value (should be valid JavaScript)
     * @return _JsLiteral
     */
    public static function literal($str) {
        return new _JsLiteral($str);
    }

    private static function is_assoc($arr) {
        $i = 0;
        foreach($arr as $k => $v) {
            if($k !== $i++) return true;
        }
        return false;
    }
}

if(!class_exists('JsonException')) {
    class JsonException extends Exception {}
}

if(!interface_exists('JsonSerializable')) {
    /**
     * Objects implementing JsonSerializable can customize their JSON representation when encoded with `json_encode()` or `Json::encode`.
     */
    interface JsonSerializable {
        /**
         * Serializes the object to a value that can be serialized natively by `json_encode()`.
         * @return mixed Data which can be serialized by `json_encode()`, which is a value of any type other than a resource.
         */
        public function jsonSerialize();
    }
}

/**
 * Internal class used to prevent JSON-encoding (do not use directly)
 * @internal
 */
class _JsLiteral {
    public $str;
    function __construct($str) {
        $this->str = $str;
    }
}