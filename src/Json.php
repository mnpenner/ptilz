<?php
namespace Ptilz;
use Ptilz\Exceptions\JsonException;
use JsonSerializable;

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
    public static function encode($var, $options = 0) {
        if(is_array($var)) {
            if(self::isAssoc($var)) {
                $bits = array();
                foreach($var as $k => $v) {
                    $bits[] = json_encode($k, $options) . ':' . self::encode($v);
                }
                return '{' . implode(',', $bits) . '}';
            } else {
                return '[' . implode(',', array_map(__METHOD__, $var)) . ']';
            }
        }
        if(is_object($var)) {
            if($var instanceof RawString) {
                return (string)$var;
            }
            if($var instanceof JsonSerializable) {
                return json_encode($var->jsonSerialize(), $options);
            }
        }
        return json_encode($var, $options);
    }

    /**
     * Returns a "literal" or "raw" value which will not be escaped by Json::encode
     *
     * @param string $str Raw value (should be valid JavaScript)
     * @return RawString
     */
    public static function raw($str) {
        return new RawString($str);
    }

    private static function isAssoc($arr) {
        $i = 0;
        foreach($arr as $k => $v) {
            if($k !== $i++) return true;
        }
        return false;
    }

    private static $error_codes = [
        JSON_ERROR_NONE => "No error has occurred",
        JSON_ERROR_DEPTH => "The maximum stack depth has been exceeded",
        JSON_ERROR_STATE_MISMATCH => "Invalid or malformed JSON",
        JSON_ERROR_CTRL_CHAR => "Control character error, possibly incorrectly encoded",
        JSON_ERROR_SYNTAX => "Syntax error",
        JSON_ERROR_UTF8 => "Malformed UTF-8 characters, possibly incorrectly encoded",
        JSON_ERROR_RECURSION => "One or more recursive references in the value to be encoded",
        JSON_ERROR_INF_OR_NAN => "One or more NAN or INF values in the value to be encoded",
        JSON_ERROR_UNSUPPORTED_TYPE => "A value of a type that cannot be encoded was given",

    ];

    public static function decode($str) {
        $result = json_decode($str, true);
        $error_code = json_last_error();
        if($error_code !== JSON_ERROR_NONE) {
            $error_string = array_key_exists($error_code, self::$error_codes) ? self::$error_codes[$error_code] : "Unknown error code $error_code";
            throw new JsonException("\"$error_string\" decoding JSON " . $str);
        }
        return $result;
    }
}

if(!class_exists('Ptilz\Exceptions\JsonException')) {
    class JsonException extends Exception {
    }
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

