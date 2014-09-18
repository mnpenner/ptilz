<?php
namespace Ptilz;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Exceptions\InvalidOperationException;
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
     * Convert a string to 1s and 0s.
     *
     * @param string $str Binary string
     * @param string $sep Byte separator
     * @return string
     */
    public static function binary($str,$sep=' ') {
        $bitSeq = [];
        $len = strlen($str);
        for($i = 0; $i < $len; $i++) {
            $bitStr = decbin(ord($str[$i]));
            $bitSeq[] = substr('00000000', 0, 8 - strlen($bitStr)) . $bitStr;
        }
        return implode($sep, $bitSeq);
    }

    /**
     * @param string $format
     * @param mixed  ...$args
     * @return string
     */
    public static function format($format) {
        return self::formatArgs($format, array_slice(func_get_args(), 1));
    }

    /**
     * @param string $format
     * @param array $args
     * @return string
     */
    public static function formatArgs($format, array $args) {
        if(!$args) return $format;
        $i = 0;
        $patt = <<<'REGEX'
        ~(?J)
            \{
                (?<arg> [^}:]* )
                (?:
                    :
                    (?:
                        (?<fmt> [TVotsc] )
                        | (?<fmt> [Xx] ) (?<pad> \d+ )?
                        | (?<fmt> [b] ) (?<sep> [^}]* )
                        | (?<fmt> [fF] ) (?<opt> [^}]+ )?
                        | (?<fmt> [i] ) (?<opt> [^}]+ )?
                        | (?<fmt> [n] ) (?:
                            (?<dec> \d+ ) (?: (?<pt> . ) (?<sep> .? ) )?
                          )?
                    )
                )?
            \}
        ~x
REGEX;
        return preg_replace_callback($patt, function ($m) use ($format, $args, &$i) {
            $val = $m['arg'] === '' ? $args[$i++] : $args[$m['arg']];
//            var_dump($m);
            if(array_key_exists('fmt',$m)) {
                switch($m['fmt']) {
                    case 'T':
                        return V::getType($val);
                    case 'i':
                        if(!is_int($val)) $val = intval($val);
                        if(array_key_exists('opt',$m)) {
                            return sprintf('%' . $m['opt'] . 'd', $val);
                        }
                        break;
                    case 'f':
                    case 'F':
                        if(!is_float($val)) $val = floatval($val);
                        if(array_key_exists('opt',$m)) {
                            return sprintf('%' . $m['opt'] . $m['fmt'], $val);
                        }
                        break;
                    case 'V':
                        return V::toString($val);
                    case 's':
                        if(!is_string($val)) $val = strval($val);
                        break;
                    case 't':
                        return V::export($val);
                    case 'n':
                        if(array_key_exists('dec',$m)) {
                            if(!array_key_exists('pt',$m)) {
                                return number_format($val, $m['dec']);
                            }
                            return number_format($val, $m['dec'], $m['pt'], $m['sep']);
                        }
                        return number_format($val);
                    case 'b':
                        if(is_int($val)) return decbin($val);
                        if(is_string($val)) return self::binary($val,$m['sep']);
                        throw new InvalidOperationException(Str::format("Cannot convert value of type {:T} to binary string", $val));
                    case 'o':
                        return decoct($val);
                    case 'c':
                        return chr($val);
                    case 'x':
                    case 'X':
                        if(is_int($val)) $hex = dechex($val);
                        elseif(is_string($val)) $hex = bin2hex($val);
                        else throw new InvalidOperationException(Str::format("Cannot convert value of type {:T} to hexidecimal", $val));
                        if($m['fmt'] === 'X') $hex = strtoupper($hex);
                        if(array_key_exists('pad',$m)) {
                            $hex = str_pad($hex, (int)$m['pad'], '0', STR_PAD_LEFT);
                        }
                        return $hex;
                }
            }
            if(!is_string($val) || Str::isBinary($val)) return V::toString($val);
            return $val;
        }, $format);
    }

    /**
     * Adds backslashes before unprintable characters.
     *
     * @param $str String to escape
     * @param string $add Additional characters to escape
     * @return mixed
     */
    public static function addSlashes($str, $add='\\') {
        $patt = '~[^\x20-\x7E]';
        if($add !== '') $patt .= '|[' . preg_quote($add, '~') . ']';
        $patt .= '~';
        return preg_replace_callback($patt, function ($m) {
            switch($m[0]) {
                case "\n": return '\n';
                case "\r": return '\r';
                case "\t": return '\t';
                case "\v": return '\v';
                case "\x1B": return '\e';
                case "\f": return '\f';
                case '$': return '\$';
                case '"': return '\\"';
                case "'": return "\\'";
                case "\0": return '\0';
                case '\\': return '\\\\';
            }
            return '\\x' . strtoupper(bin2hex($m[0]));
        }, $str);
    }

    /**
     * Determines if a string contains non-printable characters.
     *
     * @param string $str
     * @return bool
     */
    public static function isBinary($str) {
        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }

    /**
     * Converts a string to its PHP source code representation.
     *
     * @param string $str
     * @return string
     */
    public static function export($str) {
        return '"'.self::addSlashes($str,'"\\$').'"';
    }
}