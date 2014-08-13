<?php
namespace Ptilz;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Exceptions\NotImplementedException;

/**
 * String helper methods.
 */
abstract class Str {
    public static function startsWith($haystack, $needle, $case_insensitive = false) {
        $substr = substr($haystack, 0, strlen($needle));
        return $case_insensitive
            ? mb_strtolower($substr) === mb_strtolower($needle)
            : $substr === $needle;
    }

    public static function endsWith($haystack, $needle, $case_insensitive = false) {
        $substr = substr($haystack, -strlen($needle));
        return $case_insensitive
            ? mb_strtolower($substr) === mb_strtolower($needle)
            : $substr === $needle;
    }

    public static function phpTemplate($__file__, $__vars__) {
        extract($__vars__, EXTR_SKIP);
        ob_start();
        include $__file__;
        return ob_get_clean();
    }

    /**
     * Splits a string into an array of characters. Works on multi-byte strings.
     *
     * @param $str
     * @return array
     */
    public static function splitChars($str) {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Gets the length of a string. Works on multi-byte characters.
     *
     * @param string $str The string being checked for length
     * @param string $encoding Character encoding
     * @return int
     */
    public static function length($str, $encoding='UTF-8') {
        return function_exists('mb_strlen') ? mb_strlen($str,$encoding) : preg_match_all('/./us', $str);
    }

    public static function cEscapeStr($str) {
        return '"' . addcslashes($str, "\0..\37\42\134\177..\377") . '"';
    }

    /**
     * Split a string into an array using a delimiter, working from right to left, up to the specified number of elements.
     *
     * @param string $str The input string.
     * @param string $delim The boundary string.
     * @param int $limit Maximum of limit elements with the last element containing the rest of string.
     * @param mixed $pad If $limit and $pad are provided, the result will be padded up to the limit with this value. Useful when used with list(...) = rsplit(...) to avoid warnings.
     * @return array
     */
    public static function rsplit($str, $delim, $limit = PHP_INT_MAX, $pad = null) {
        $parts = array();
        for($i = $limit; $i > 1; --$i) {
            $pos = strrpos($str, $delim);
            if($pos === false) break;
            array_unshift($parts, substr($str, $pos + 1));
            $str = substr($str, 0, $pos);
        }
        array_unshift($parts, $str);
        if(func_get_args() >= 4 && $limit !== PHP_INT_MAX && count($parts) < $limit) {
            $parts = array_pad($parts, $limit, $pad);
        }
        return $parts;
    }

    /**
     * @static
     * @param array $dict Search => Replace pairs
     * @param string $input Input
     * @return string
     */
    public static function replace($dict, $input) {
        return str_replace(array_keys($dict), array_values($dict), $input);
    }

    /**
     * Generate a random string from the given alphabet.
     *
     * @param int $len String length
     * @param string $chars Characters to choose from
     * @return string Random string
     * @throws ArgumentOutOfRangeException
     */
    public static function random($len, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
        if($len < 0) throw new ArgumentOutOfRangeException('len',"Length must be non-negative");
        $str = '';
        $randMax = strlen($chars) - 1;

        while($len--) {
            $str .= $chars[mt_rand(0, $randMax)];
        }

        return $str;
    }

    /**
     * Generates a cryptographically secure random string from the alphabet ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_
     *
     * @param $len String length
     * @throws ArgumentOutOfRangeException
     * @return string
     */
    public static function securand($len) {
        if($len < 0) throw new ArgumentOutOfRangeException('len',"Length must be non-negative");
        return strtr(substr(base64_encode(openssl_random_pseudo_bytes(ceil($len * 3 / 4))), 0, $len), '+/', '-_');
    }

    /**
     * Checks if a string is null, empty or all whitespace.
     *
     * @param string $str
     * @return bool
     */
    public static function isEmpty($str) {
        return $str === null || trim($str) === '';
    }

    /**
     * Checks if a string looks like an integer. Scientic notation (1e2), binary (0b1), and hexidecimal (0xf) are not allowed. Octal notation (leading zero) will be treated like a decimal number.
     *
     * @param  string $str String to test
     * @param bool $allowWhitespace Allow leading and trailing whitespace
     * @param bool $allowNeg Allow negative sign (-) immediately before the number
     * @return bool
     */
    public static function isInt($str, $allowWhitespace=true, $allowNeg=true) {
        if(is_int($str)) return true;
        $patt = '~';
        if($allowWhitespace) $patt .= '\s*';
        if($allowNeg) $patt .= '-?';
        $patt .= '\d+';
        if($allowWhitespace) $patt .= '\s*';
        $patt .= '\z~A';
        return preg_match($patt, $str) === 1;
    }

    /**
     * @param string $format
     * @param mixed  ...$args
     * @return string
     */
    public static function format($format) {
        $args = array_slice(func_get_args(), 1);
        if(!$args) return $format;
        $i = 0;
        return preg_replace_callback('~\{([^}]*)\}~', function ($m) use ($format, $args, &$i) {
            return $m[1] === '' ? $args[$i++] : $args[$m[1]];
        }, $format);
    }
}