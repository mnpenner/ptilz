<?php
namespace Ptilz;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Exceptions\InvalidOperationException;
use Ptilz\Exceptions\NotImplementedException;
use Ptilz\Exceptions\NotSupportedException;

/**
 * String helper methods.
 * Use mb_internal_encoding() to set the default encoding.
 */
abstract class Str {

    /**
     * Ascii85, ZeroMQ version.
     *
     * @see http://en.wikipedia.org/wiki/Ascii85#ZeroMQ_Version_.28Z85.29
     * @see http://rfc.zeromq.org/spec:32
     */
    const Z85 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-:+=^!/*?&<>()[]{}@%$#';

    /**
     * Ascii85, Adobe version.
     *
     * @see http://en.wikipedia.org/wiki/Ascii85#Adobe_version
     */
    const ADOBE85 = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstu';

    /**
     * Printable ASCII characters, excluding space (0x21 - 0x7E).
     */
    const ASCII96 = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~';

    /**
     * Printable ASCII characters (0x20 - 0x7E).
     *
     * @see http://en.wikipedia.org/wiki/ASCII#ASCII_printable_characters
     */
    const ASCII_PRINTABLE = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~';

    /**
     * Character set used by base64_encode.
     */
    const BASE64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    /**
     * Standard 'base64url' with URL and Filename Safe Alphabet (RFC 4648 §5 'Table 2: The "URL and Filename safe" Base 64 Alphabet')
     *
     * @see http://tools.ietf.org/html/rfc3548#page-6
     */
    const BASE64URL = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';



    // see also: http://php.net/manual/en/regexp.reference.character-classes.php and http://php.net/manual/en/regexp.reference.escape.php

    /**
     * Character codes 0 - 127
     */
    const ASCII = "\0\x01\x02\x03\x04\x05\x06\x07\x08\t\n\v\f\r\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F !\"#\$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~\x7F";

    /**
     * Lowercase letters
     */
    const ASCII_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';

    /**
     * Uppercase letters
     */
    const ASCII_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Upper and lowercase letters.
     */
    const ASCII_LETTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Uppercase letters (A-Z), lowercase letters (a-z), and numbers (0-9).
     */
    const ALPHANUM = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    /**
     * Numbers 0-9
     */
    const DIGITS = '0123456789';

    /**
     * Hexadecimal characters: 0-9, a-f, A-F
     */
    const HEXDIGITS = '0123456789abcdefABCDEF';

    /**
     * Base 8 numbers, 0-8
     */
    const OCTDIGITS = '012345678';

    /**
     * Word characters
     */
    const WORD_CHARACTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_';

    /**
     * Space (\x20), tab (\t), new line/line feed (\n), carriage return (\r), vertical tab/line tabulation (\x0B), form feed (\x0C).
     * Excludes NUL-byte (\x00), extended ASCII, and unicode characters.
     *
     * @see http://en.wikipedia.org/wiki/Whitespace_character
     */
    const WHITESPACE = "\x09\x0A\x0B\x0C\x0D\x20";

    /**
     * Tests if a string starts with the given prefix.
     *
     * @param string $subject
     * @param string $prefix
     * @param bool $ignore_case
     * @param string $encoding Character encoding
     * @return bool
     */
    public static function startsWith($subject, $prefix, $ignore_case = false, $encoding = null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        $substr = mb_substr($subject, 0, mb_strlen($prefix, $encoding), $encoding);
        return $ignore_case
            ? mb_strtolower($substr, $encoding) === mb_strtolower($prefix, $encoding)
            : $substr === $prefix;
    }

    /**
     * Tests if a string ends with the given postfix.
     *
     * @param string $subject
     * @param string $postfix
     * @param bool $ignore_case
     * @param string $encoding Character encoding
     * @return bool
     */
    public static function endsWith($subject, $postfix, $ignore_case = false, $encoding = null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        $substr = mb_substr($subject, -mb_strlen($postfix, $encoding), null, $encoding);
        return $ignore_case
            ? mb_strtolower($substr, $encoding) === mb_strtolower($postfix, $encoding)
            : $substr === $postfix;
    }

    /**
     * Renders a PHP file to a string using the given variables.
     *
     * @param string $__file__ PHP filename to render
     * @param array  $__vars__ Variables to extract into local/global scope
     * @param bool   $html_escape Recursively HTML-escape and nl2br all variables
     *
     * @return string
     */
    public static function phpTemplate($__file__, $__vars__, $html_escape=false) {
        if($html_escape) {
            array_walk_recursive($__vars__, function (&$v) {
                $v = nl2br(htmlspecialchars($v));
            });
        }
        unset($html_escape);
        extract($__vars__, EXTR_SKIP);
        unset($__vars__);
        ob_start();
        include $__file__;
        return ob_get_clean();
    }

    /**
     * Tests if a variable is castable to a string.
     *
     * @param mixed $var
     * @return bool
     */
    public static function castable($var) {
        return $var === null || is_scalar($var) || is_callable([$var, '__toString']);
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
     * Splits a search query into separate terms. Supports quoting and escape sequences.
     *
     * @param string $query
     * @return array
     */
    public static function splitSearchQuery($query) {
        $terms = [];

        $quo = null;
        $esc = false;

        $term = '';

        foreach(self::splitChars($query) as $ch) {
            switch($ch) {
                case ' ': // space
                case "\n": // new line/line feed
                case "\r": // carriage return
                case "\0": // null byte
                case "\x0B": // vertical tab
                    if($esc || $quo) {
                        $term .= $ch;
                    } elseif(strlen($term)) {
                        $terms[] = $term;
                        $term = '';
                    }
                    break;
                case '"':
                case "'":
                    if($esc) {
                        $term .= $ch;
                    } elseif($quo) {
                        if($quo === $ch) { // end quote
                            if(strlen($term)) {
                                $terms[] = $term;
                                $term = '';
                            }
                            $quo = null;
                        } else { // quoted quote
                            $term .= $ch;
                        }
                    } else { // start quote
                        if(strlen($term)) {
                            $terms[] = $term;
                            $term = '';
                        }
                        $quo = $ch;
                    }
                    break;
                case '\\':
                    if($esc) {
                        $term .= $ch;
                        $esc = false;
                    } else {
                        $esc = true;
                    }
                    break;
                default:
                    if($esc) {
                        switch($ch) { // custom escape sequences! because why not?
                            case 'n':
                                $term .= "\n";
                                break;
                            case 'r':
                                $term .= "\r";
                                break;
                            case 't':
                                $term .= "\t";
                                break;
                            case '0':
                                $term .= "\0";
                                break;
                            default:
                                $term .= $ch;
                                break;
                        }
                        $esc = false;
                    } else {
                        $term .= $ch;
                    }
                    break;
            }
        }

        if(strlen($term)) {
            $terms[] = $term;
        }

        return $terms;
    }

    /**
     * Gets the length of a string. Works on multi-byte characters.
     *
     * @param string $str The string being checked for length
     * @param string $encoding Character encoding
     * @return int
     */
    public static function length($str, $encoding=null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        return function_exists('mb_strlen') ? mb_strlen($str,$encoding) : preg_match_all('/./us', $str);
    }

    public static function cEscapeStr($str) {
        return '"' . addcslashes($str, "\0..\37\42\134\177..\377") . '"';
    }

    /**
     * Split a string into an array using a delimiter, working from left to right, up to the specified number of elements.
     *
     * @param string $str   The input string.
     * @param string $delim The boundary string.
     * @param int $limit    Maximum of limit elements with the last element containing the rest of string.
     * @param mixed $pad    If $limit and $pad are provided, the result will be padded up to the limit with this value. Useful when used with list(...) = rsplit(...) to avoid warnings.
     * @return array
     * @throws NotImplementedException
     */
    public static function split($str, $delim, $limit = PHP_INT_MAX, $pad = null) {
        throw new NotImplementedException();
    }

    /**
     * Join the elements of a traversable to form a string.
     *
     * @param array|\Traversable $trav
     * @param string $glue
     * @return string
     */
    public static function join($trav, $glue = '') {
        return implode($glue, is_array($trav) ? $trav : iterator_to_array($trav, false));
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
        $parts = [];
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
     * @param array $dict   Search => Replace pairs
     * @param string $input Input
     * @param string $encoding
     * @param int $count
     * @return string
     */
    public static function replace($dict, $input, $encoding=null, &$count=0) {
        if($encoding === null) $encoding = mb_internal_encoding();
        return self::mbReplace(array_keys($dict), array_values($dict), $input, $encoding, $count);
    }

    /**
     * Generate a random string from the given alphabet.
     *
     * This function chooses one character at a time, making it a little bit slow ~ O(len)
     *
     * @param int $len String length
     * @param string $chars Characters to choose from
     * @return string Random string
     * @throws ArgumentOutOfRangeException
     */
    public static function random($len, $chars = self::ALPHANUM) {
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
     * @deprecated
     */
    public static function securand($len) {
        if($len < 0) throw new ArgumentOutOfRangeException('len',"Length must be non-negative");
        return self::secureRandom($len*8, self::BASE64URL);
    }

    /**
     * Generates a secure, randomly generated string from the given character set.
     *
     * @param int $bits Number of *bits* to generate.
     * @param string $alphabet Set to draw characters from.
     * @return string
     */
    public static function secureRandom($bits, $alphabet) {
        $bytes = (int)ceil($bits/8);
        $data = Bin::secureRandomBytes($bytes);
        $stream = new BitStream($data, $bits);
        return self::encode($stream, $alphabet);
    }

    /**
     * Encodes binary data with the given alphabet.
     *
     * @param string|BitStream $data Data to encode. Use a BitStream if you wish to omit trailing bits from the encoding.
     * @param string $alphabet Characters to encode with. Identical to base64_encode minus the padding when you use Str::BASE64.
     * @return string String with length betweeen `ceil($src_bits/ceil($alpha_bits))` and `ceil($src_bits/floor($alpha_bits))` characters, where $alpha_bits = log(strlen($alpha),2) and $src_bits = strlen($data)*8 unless a BitStream is used.
     * @throws \Ptilz\Exceptions\ArgumentOutOfRangeException
     */
    public static function encode($data, $alphabet) {
        if($data instanceof BitStream) {
            $data->rewind();
        } else {
            $data = new BitStream($data);
        }

        $n = strlen($alphabet);
        if($n < 2) {
            throw new ArgumentOutOfRangeException('alphabet',Str::format("Alphabet must contain at least 2 characters; got {:V}",$alphabet));
        }
        $k = (int)floor(log($n,2));
        $u = (2 << $k) - $n;
        $out = '';

        // see http://en.wikipedia.org/wiki/Truncated_binary_encoding#Example_with_n_.3D_10

        while(!$data->eof()) {
            $i = $data->read($k);

            if($i >= $u) {
                $i = ($i << 1 | $data->read(1)) - $u;
            }

            $out .= $alphabet[$i];
        }

        return $out;
    }


    /**
     * Checks if a string is null, empty or all whitespace.
     *
     * @param string $str
     * @return bool
     */
    public static function isBlank($str) {
        return $str === null || trim($str," \t\n\r\0\x0B\x0C") === '';
    }

    /**
     * Checks if a string is null or empty ("")
     *
     * @param string $str
     * @return bool
     */
    public static function isEmpty($str) {
        return $str === null || $str === '';
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
        // fixme: make more like C# String.Format? http://msdn.microsoft.com/en-us/library/system.string.format%28v=vs.110%29.aspx
        // use NumberFormatter? http://php.net/manual/en/class.numberformatter.php
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
                case "\v": return '\v'; // since PHP 5.2.5
                case "\x1B": return '\e'; // since PHP 5.4.4
                case "\f": return '\f'; // since PHP 5.2.5
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
        // alternatively, we can use http://php.net/manual/en/function.mb-check-encoding.php or (bool) preg_match('//u', $str);
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

    /**
     * Adds quotes around a string.
     *
     * @param string $string
     * @param string $quoteChar
     * @return string
     */
    public static function quote($string, $quoteChar = '"') {
        return $quoteChar.$string.$quoteChar;
    }

    /**
     * @param string $string
     * @param null|string $quoteChar
     * @return string
     */
    public static function unquote($string, $quoteChar = '"') {
        if(strlen($string) >= 2 && $string[0] === $quoteChar && $string[strlen($string) - 1] === $quoteChar) {
            return substr($string, 1, -1);
        }
        return $string;
    }

    /**
     * Checks if string is valid UTF-8. If yes, returns string as is, otherwise assumes string is ISO-8859-1 and converts to UTF-8.
     *
     * Avoid this method if possible. It's only useful to prevent encoding errors when you don't know where your strings are coming from.
     * Mixed strings will become garbled.
     *
     * @param string $str
     * @return string
     */
    public static function forceUtf8($str) {
        if(!mb_check_encoding($str, 'UTF-8')) {
            return utf8_encode($str);
        }
        return $str;
    }

    /**
     * Converts underscored or dasherized string to a camelized one. Begins with a lower case letter unless it starts with an underscore or string
     *
     * @param string $str
     * @throws NotImplementedException
     * @return string
     */
    public static function camelize($str) {
        throw new NotImplementedException;
    }

    /**
     * Converts string to camelized class name. First letter is always upper case
     *
     * @param string $str
     * @throws NotImplementedException
     * @return string
     */
    public static function classify($str) {
        return implode('',array_map('ucfirst',self::splitCodeWords($str)));
    }

    protected static function removeDiacritics($str, $encoding=null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        return iconv($encoding, 'ASCII//TRANSLIT//IGNORE', $str);
    }

    protected static function splitCodeWords($str) {
        $str = str_replace("'",'',$str); // strip apostrophes
        $str = preg_replace_callback('~\p{Lu}+~u',function($m) {
            $w = mb_strtolower($m[0]);
            return ' ' . (mb_strlen($w) > 1 ? mb_substr($w, 0, -1) . ' ' . mb_substr($w, -1) : $w);
        },$str); // split CamelCase words
        $str = preg_replace('~\A[^\pL\pN]+|[^\pL\pN]+\z~u','',$str); // trim punctuation off ends
        return preg_split('~[^\pL\pN]+~u',$str);
    }

    /**
     * Converts a camelized or dasherized string into an underscored one
     *
     * @param string $str
     * @throws NotImplementedException
     * @return string
     */
    public static function underscored($str) { // snakeCase? https://lodash.com/docs#snakeCase
        return mb_strtolower(implode('_',self::splitCodeWords($str)));
    }

    /**
     * Converts a underscored or camelized string into an dasherized one. Will start with leading dash if the first letter is uppercase.
     *
     * @param string $str
     * @throws NotImplementedException
     * @return string
     */
    public static function dasherized($str) { // TODO: rename to kebabCase? https://lodash.com/docs#kebabCase
        $str = str_replace("'", '', $str); // strip apostrophes
        $str = preg_replace('~\A[^\pL\pN]+|[^\pL\pN]+\z~u', '', $str); // trim punctuation off ends
        $str = preg_replace_callback('~\p{Lu}+~u', function ($m) {
            $w = mb_strtolower($m[0]);
            return '-' . (mb_strlen($w) > 1 ? mb_substr($w, 0, -1) . '-' . mb_substr($w, -1) : $w);
        }, $str); // split CamelCase words
        return mb_strtolower(preg_replace('~[^\pL\pN]+~u', '-', $str));
    }

    /**
     * Multi-byte safe uppercase first character.
     *
     * @param $string
     * @param null|string $encoding Defaults to mb_internal_encoding().
     * @return string
     */
    protected static function mb_ucfirst($string, $encoding=null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, mb_strlen($string)-1, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    /**
     * Converts an underscored, camelized, or dasherized string into a humanized one. Also removes beginning and ending whitespace, and removes the postfix '_id'.
     *
     * @param string $str
     * @throws NotImplementedException
     * @return string
     */
    public static function humanize($str) {
        if(preg_match('~[-_]id~i',$str)) {
            $str = substr($str,0,-3);
        }
        return self::mb_ucfirst(mb_strtolower(implode(' ',self::splitCodeWords($str))));
    }

    /**
     * Title-cases a phrase
     *
     * @param string $str
     * @throws NotImplementedException
     * @return string
     */
    public static function titleize($str) {
        throw new NotImplementedException;
    }

    /**
     * Truncates a string to the specified length. Adds an ellipsis if the string is too long.
     *
     * @param string $str String to truncate
     * @param int $len Maximum character length
     * @param string $end
     * @param bool $avoid_word_cut
     * @param string $encoding
     * @return string
     */
    public static function truncate($str, $len, $end='…', $avoid_word_cut=true, $encoding=null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        $strlen = mb_strlen($str, $encoding);
        if($avoid_word_cut && $strlen > $len) {
            $pos = mb_strrpos($str,' ',$len-$strlen, $encoding);
            if($pos !== false) {
                $len = $pos;
            }
        }
        if($strlen <= $len + mb_strlen($end, $encoding)) {
            return $str;
        }
        return preg_replace('~\W\z~u','',mb_substr($str, 0, $len, $encoding)) . $end;
    }

//    public static function rePos($subject, $pattern, $offset=0) {
//        if(preg_match($pattern,$subject,$m,PREG_OFFSET_CAPTURE,$offset)) {
//            return $m[0][1];
//        }
//        return false;
//    }
//
//    public static function rePosRev($subject, $pattern, $offset=null) {
//        if($offset === null) $offset = strlen($subject);
//        $search_str = strrev(substr($subject, 0, $offset));
//        if(preg_match($pattern, $search_str, $m, PREG_OFFSET_CAPTURE)) {
//            return $offset - $m[0][1];
//        }
//        return false;
//    }

    /**
     * Transform text into a URL slug. Removes accents and replaces whitespaces with dashes.
     *
     * @param string $str
     * @return string
     */
    public static function slugify($str) {
        return trim(preg_replace('~[^a-z0-9]+~','-',strtolower(str_replace("'",'',self::removeDiacritics($str)))),'-');
    }

    /**
     * Takes a string and converts it to a bool. If the string doesn't look true nor false, it returns null.
     *
     * @param mixed $val        The string to convert
     * @param null $default
     * @return bool             true if $val is in (1,t,true,y,yes,on), false if $str is in (0,f,false,n,no,false), otherwise $default
     */
    public static function toBoolean($val, $default=null) {
        if(is_string($val)) {
            $str = strtolower(trim($val));
            if(in_array($str,['1','t','true','y','yes','on'])) return true;
            if(in_array($str,['0','f','false','n','no','off'])) return false;
        }
        elseif(is_bool($val)) return $val;
        elseif(is_int($val)) return $val !== 0;
        elseif(is_array($val)) return $val !== [];
        return $default;
    }

    /**
     * Compress some whitespaces to one.
     *
     * @param string $str
     * @return string
     * @deprecated Renamed to collapseWhitespace to be consistent w/ W3C
     */
    public static function compressWhitespace($str) {
        return self::collapseWhitespace($str);
    }

    /**
     * Compress some whitespaces to one.
     *
     * @param string $str
     * @return string
     */
    public static function collapseWhitespace($str) {
        return preg_replace('~[ \t\n\r\0\x0B\x0C]+~',' ',trim($str," \t\n\r\0\x0B\x0C"));
    }

    /**
     * Tests if a string contains a substring.
     * @param string $str String to search
     * @param string $needle Substring to search for
     * @param bool $case_sensitive Perform case sensitive search
     * @return bool
     */
    public static function contains($str, $needle, $case_sensitive=true) {
        return $case_sensitive
            ? strpos($str, $needle) !== false
            : stripos($str, $needle) !== false;
    }

    /**
     * Split a string into lines.
     *
     * @param string $str
     * @return array
     */
    public static function lines($str) {
        return preg_split('~\R~',$str);
    }

    /**
     * Reverse a string.
     *
     * @param string $string
     * @param null|string $encoding
     * @return string
     * @credit http://kvz.io/blog/2012/10/09/reverse-a-multibyte-string-in-php/
     */
    public static function reverse($string, $encoding = null) {
        if($encoding === null) $encoding = mb_internal_encoding();

        $length = mb_strlen($string, $encoding);
        $reversed = '';
        while($length-- > 0) {
            $reversed .= mb_substr($string, $length, 1, $encoding);
        }

        return $reversed;
    }

    /**
     * Splices a string.
     *
     * @param string $string String to splice
     * @param int $index Index where splice starts
     * @param int $howmany How many characters to replace (use 0 for insert)
     * @param string $substring Sub-string to replace spliced characters with
     * @return string
     */
    public static function splice($string, $index, $howmany, $substring) {
        return substr($string,0,$index).$substring.substr($string,$index+$howmany);
    }

    /**
     * Split a string into words.
     *
     * @param $str
     * @return array
     */
    public static function words($str) {
        return preg_split('~\s+~',trim($str));
    }

    /**
     * Repeat a string.
     *
     * @param string $string
     * @param int $count
     * @param string $separator
     * @return string
     */
    public static function repeat($string, $count, $separator='') {
        return str_repeat($string.$separator, $count-1).$string;
    }


    /**
     * Trims a prefix off the start of the string if it exists
     *
     * @param string $str    String with prefix
     * @param string $prefix Prefix to remove
     *
     * @return string    String without prefix. Unaltered if prefix not found.
     */
    public static function removePrefix($str,$prefix) {
        return self::startsWith($str,$prefix) ? mb_substr($str,mb_strlen($prefix)) : $str;
    }

    /**
     * Trims a postfix off the start of the string if it exists
     *
     * @param string $str     String with postfix
     * @param string $postfix Postfix to remove
     *
     * @return string    String without postfix. Unaltered if postfix not found.
     */
    public static function removePostfix($str,$postfix) {
        return self::endsWith($str,$postfix) ? mb_substr($str,0,-mb_strlen($postfix)) : $str;
    }

    /**
     * Trim the minimum number of leading spaces from each line
     *
     * @param string $str String
     *
     * @return string    String with leading spaces removed
     */
    public static function trimLeadingWhitespace($str) {
        if(trim($str)==='') return '';
        preg_match_all('`^[ \t]*(?=\S)`m',$str,$matches,PREG_PATTERN_ORDER);
        if($matches[0][0]==='') array_shift($matches[0]);
        if($matches[0]) {
            $min = min(array_map('strlen',$matches[0]));
            return preg_replace('`^[ \t]{'.$min.'}`m','',$str);
        } else {
            return $str;
        }
    }

    /**
     * Removes blank lines from the start and end of a multi-line string.
     *
     * @param string $str
     * @return string
     */
    public static function trimBlankLines($str) {
        return preg_replace('`\A\s*^|$\s*\z`m','',$str);
    }

    /**
     * Removes excess whitespace surrounding a block of code.
     *
     * @param string $str
     * @return string
     */
    public static function dedent($str) {
        return self::trimBlankLines(self::trimLeadingWhitespace($str));
    }

    /**
     * Replace all occurrences of the search string with the replacement string. Multibyte safe.
     *
     * @param string|array $search The value being searched for, otherwise known as the needle. An array may be used to designate multiple needles.
     * @param string|array $replace The replacement value that replaces found search values. An array may be used to designate multiple replacements.
     * @param string|array $subject The string or array being searched and replaced on, otherwise known as the haystack.
     *                              If subject is an array, then the search and replace is performed with every entry of subject, and the return value is an array as well.
     * @param string $encoding The encoding parameter is the character encoding. If it is omitted, the internal character encoding value will be used.
     * @param int $count If passed, this will be set to the number of replacements performed.
     * @return array|string
     */
    public static function mbReplace($search, $replace, $subject, $encoding = null, &$count=0) {
        if($encoding === null) $encoding = mb_internal_encoding();
        if(!is_array($subject)) {
            $searches = is_array($search) ? array_values($search) : [$search];
            $replacements = is_array($replace) ? array_values($replace) : [$replace];
            $replacements = array_pad($replacements, count($searches), '');
            foreach($searches as $key => $search) {
                $replace = $replacements[$key];
                $search_len = mb_strlen($search, $encoding);

                $sb = [];
                while(($offset = mb_strpos($subject, $search, 0, $encoding)) !== false) {
                    $sb[] = mb_substr($subject, 0, $offset, $encoding);
                    $subject = mb_substr($subject, $offset + $search_len, null, $encoding);
                    ++$count;
                }
                $sb[] = $subject;
                $subject = implode($replace, $sb);
            }
        } else {
            foreach($subject as $key => $value) {
                $subject[$key] = self::mbReplace($search, $replace, $value, $encoding, $count);
            }
        }
        return $subject;
    }
}
