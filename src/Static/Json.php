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
                    $bits[] = self::encode((string)$k, $options) . ':' . self::encode($v, $options);
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
        if(is_string($var)) {
            $var = self::_utf8($var);
        }

        $result = json_encode($var, $options);
        $error_code = json_last_error();
        if($error_code !== JSON_ERROR_NONE) {
            throw new InvalidOperationException(json_last_error_msg(), $error_code);
        }
        return $result;
    }

    private static function _utf8($str) {
        if(!mb_check_encoding($str, 'UTF-8')) {
            return utf8_encode($str, 'UTF-8');
        }
        return $str;
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
            throw new InvalidOperationException(json_last_error_msg(), $error_code);
        }
        return $result;
    }
}
