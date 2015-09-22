<?php
namespace Ptilz;

use JsonSerializable;
use Ptilz\Exceptions\FileNotFoundException;
use Ptilz\Exceptions\InvalidOperationException;
use Ptilz\Internal\RawJson;

/** Convert invalid UTF-8 byte sequences into valid UTF-8. For example, chr(200) will be converted to "\u00c8" (È) instead of throwing an error. */
define('JSON_FORCE_UTF8', 1073741824);
/** Enable UNESCAPED_SLASHES but continue to escape </script>. Reduces output size slightly while maintaining compatibility inside <script> tags. */
define('JSON_ESCAPE_SCRIPTS', 536870976);

abstract class Json {
    /** All < and > are converted to \u003C and \u003E. Available since PHP 5.3.0. */
    const HEX_TAG = JSON_HEX_TAG;
    /** All &s are converted to \u0026. Available since PHP 5.3.0. */
    const HEX_AMP = JSON_HEX_AMP;
    /** All ' are converted to \u0027. Available since PHP 5.3.0. */
    const HEX_APOS = JSON_HEX_APOS;
    /** All " are converted to \u0022. Available since PHP 5.3.0. */
    const HEX_QUOT = JSON_HEX_QUOT;
    /** Outputs an object rather than an array when a non-associative array is used. Especially useful when the recipient of the output is expecting an object and the array is empty. Available since PHP 5.3.0. */
    const FORCE_OBJECT = JSON_FORCE_OBJECT;
    /** Encodes numeric strings as numbers. Available since PHP 5.3.3. */
    const NUMERIC_CHECK = JSON_NUMERIC_CHECK;
    /** Encodes large integers as their original string value. Available since PHP 5.4.0. */
    const BIGINT_AS_STRING = JSON_BIGINT_AS_STRING;
    /** Use whitespace in returned data to format it. Available since PHP 5.4.0. */
    const PRETTY_PRINT = JSON_PRETTY_PRINT;
    /** Don't escape /. Available since PHP 5.4.0. */
    const UNESCAPED_SLASHES = JSON_UNESCAPED_SLASHES;
    /** Encode multibyte Unicode characters literally (default is to escape as \uXXXX). Available since PHP 5.4.0. */
    const UNESCAPED_UNICODE = JSON_UNESCAPED_UNICODE;
    /** Substitute some unencodable values instead of failing. Available since PHP 5.5.0. */
    const PARTIAL_OUTPUT_ON_ERROR = JSON_PARTIAL_OUTPUT_ON_ERROR;
    /** Ensures that float values are always encoded as a float value. Available since PHP 5.6.6. */
    const PRESERVE_ZERO_FRACTION = 1024;
    /** Convert invalid UTF-8 byte sequences into valid UTF-8. For example, chr(200) will be converted to "\u00c8" (È) instead of throwing an error. */
    const FORCE_UTF8 = JSON_FORCE_UTF8;
    /** Enable UNESCAPED_SLASHES but continue to escape </script>. Reduces output size slightly while maintaining compatibility inside <script> tags. */
    const ESCAPE_SCRIPTS = JSON_ESCAPE_SCRIPTS;

    /**
     * JSON-encodes a value. Escaping can be prevented on a sub-element via Json::literal.
     *
     * @param mixed $var The value being encoded. Can be any type except a resource.
     * @param int $options Options passed to `json_encode`.
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
            if($var instanceof IJavaScriptSerializable) {
                return $var->jsSerialize($options);
            }
            if($var instanceof JsonSerializable) {
                return self::encode($var->jsonSerialize(), $options);
            }
        }

        if(is_string($var) && Bin::hasFlag($options, JSON_FORCE_UTF8)) {
            $var = Str::forceUtf8($var);
        }

        $result = json_encode($var, $options);
        $error_code = json_last_error();
        if($error_code !== JSON_ERROR_NONE) {
            $message = json_last_error_msg();
            if($error_code === JSON_ERROR_UTF8) {
                $message .= " Consider using flag JSON_FORCE_UTF8.";
            }
            throw new InvalidOperationException($message, $error_code);
        }

        if(is_string($var) && Bin::hasFlag($options, JSON_ESCAPE_SCRIPTS)) {
            return str_replace('</script>', '<\/script>', $result);
        }

        return $result;
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
        // see https://github.com/symfony/HttpFoundation/blob/88d0642f6afd56488c9129ebf90839cd3f107df9/JsonResponse.php#L96-L127
        $result = json_decode($str, $assoc, $depth, $options);
        $error_code = json_last_error();
        if($error_code !== JSON_ERROR_NONE) {
            throw new InvalidOperationException(json_last_error_msg(), $error_code);
        }
        return $result;
    }

    /**
     * Loads a JSON file and decodes it.
     *
     * @param string $filename File to load
     * @param bool $assoc When TRUE, returned objects will be converted into associative arrays.
     * @param int $depth User specified recursion depth.
     * @param int $options Bitmask of JSON decode options.
     * @return mixed
     * @throws \Ptilz\Exceptions\FileNotFoundException
     */
    public static function loadFile($filename, $assoc = true, $depth = 512, $options = 0) {
        $data = @file_get_contents($filename);
        if($data === false) throw new FileNotFoundException($filename);
        return static::decode($data, $assoc, $depth, $options);
    }
}
