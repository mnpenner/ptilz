<?php
namespace Ptilz;

use JsonSerializable;
use Ptilz\Exceptions\InvalidOperationException;
use Ptilz\Internal\RawJson;

abstract class Json {
    /**
     * JSON-encodes a value. Escaping can be prevented on a sub-element via Json::literal.
     *
     * @param mixed $var The value being encoded. Can be any type except a resource.
     * @param int $options Options passed to `json_encode`. Everything except `JSON_PRETTY_PRINT` should work.
     * @throws InvalidOperationException
     * @return string
     * @see http://us3.php.net/manual/en/json.constants.php
     */
    public static function encode($var, $options = 0) {
        if(is_array($var)) {
            if(Bin::hasFlag($options, JSON_FORCE_OBJECT) || Arr::isAssoc($var)) {
                $bits = [];
                foreach($var as $k => $v) {
                    $bits[] = json_encode((string)$k, $options) . ':' . self::encode($v, $options);
                }
                return '{' . implode(',', $bits) . '}';
            } else {
                return '[' . implode(',', array_map(__METHOD__, $var)) . ']';
            }
        }
        if(is_object($var)) {
            if($var instanceof RawJson) {
                return (string)$var;
            }
            if($var instanceof JsonSerializable) {
                return self::encode($var->jsonSerialize(), $options);
            }
        }
        return json_encode($var, $options);
    }

    /**
     * Returns a "literal" or "raw" value which will not be escaped by Json::encode
     *
     * @param string $str Raw value (should be valid JavaScript)
     * @return RawJson
     */
    public static function raw($str) {
        return new RawJson($str);
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

    /**
     * Decodes a JSON string. Throws an exception on error.
     *
     * @param string $str The json string being decoded.
     * @param bool $assoc When TRUE, returned objects will be converted into associative arrays.
     * @param int $depth User specified recursion depth.
     * @param int $options Bitmask of JSON decode options.
     * @return mixed
     * @throws Exceptions\InvalidOperationException
     */
    public static function decode($str, $assoc = true, $depth = 512, $options = 0) {
        $result = json_decode($str, $assoc, $depth, $options);
        $error_code = json_last_error();
        if($error_code !== JSON_ERROR_NONE) {
            throw new InvalidOperationException(Arr::get(self::$error_codes, $error_code, "Unknown error"), $error_code);
        }
        return $result;
    }
}
