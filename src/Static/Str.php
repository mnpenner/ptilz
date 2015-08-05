<?php
namespace Ptilz;
use Ptilz\Exceptions\ArgumentFormatException;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Exceptions\InvalidOperationException;
use Ptilz\Exceptions\NotImplementedException;
use Ptilz\Exceptions\UnreachableException;

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
        return self::contains($subject, $prefix, $ignore_case, 0, $encoding);
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
        return self::contains($subject, $postfix, $ignore_case, -self::length($postfix, $encoding), $encoding);
    }

    /**
     * Tests if a string contains a substring.
     *
     * @param string $subject String to search
     * @param string $substr Substring to search for
     * @param bool $ignore_case Perform case-insensitive search
     * @param int|bool $pos If specified, substring must occur at this position (in characters)
     * @param string|null $encoding Character encoding
     * @return bool
     */
    public static function contains($subject, $substr, $ignore_case = false, $pos = false, $encoding = null) {
        if($encoding === null) {
            $encoding = mb_internal_encoding();
        }

        if($pos === false) {
            return $ignore_case
                ? mb_stripos($subject, $substr, 0, $encoding) !== false
                : mb_strpos($subject, $substr, 0, $encoding) !== false;
        }

        $subject_substr = mb_substr($subject, $pos, mb_strlen($substr, $encoding), $encoding);

        return $ignore_case
            ? mb_strtolower($subject_substr, $encoding) === mb_strtolower($substr, $encoding)
            : $subject_substr === $substr;

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

    public static function substrLen($str, $start, $length=null, $encoding=null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        if(function_exists('mb_substr')) {
            return mb_substr($str, $start, $length, $encoding);
        }
        $patt = '/.{'.$start.'}(.';
        if($length !== null) {
            if($length < 0) {
                $length += self::length($str, $encoding);
                if($length < 0) {
                    return false;
                }
            }
            $patt .= '{,'.$length.'}';
        } else {
            $patt .= '*';
        }
        $patt .= ')/Aus';
        preg_match($patt, $str, $matches);
        return isset($matches[1]) ? $matches[1] : false;
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
                    case 'T': // todo: change to lowercase 't'
                        return V::getType($val);
                    case 'i':
                        if(!is_int($val)) $val = intval($val);
                        if(array_key_exists('opt',$m)) {
                            return sprintf('%' . $m['opt'] . 'd', $val);
                        }
                        break;
                    case 'f':
                    case 'F': // todo: remove capital 'F'
                        if(!is_float($val)) $val = floatval($val);
                        if(array_key_exists('opt',$m)) {
                            return sprintf('%' . $m['opt'] . $m['fmt'], $val);
                        }
                        break;
                    case 'V': // todo: change to 'S' (for "Short" or "String")
                        return V::toString($val);
                    case 's':
                        if(!is_string($val)) $val = strval($val);
                        break;
                    case 't': // todo: change to 'e' for export
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
                        // todo: add 'd' for ord()
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
     * Escapes characters for use in a double-quoted string.
     *
     * @param $str String to escape
     * @return string Escaped string
     * @see http://php.net/manual/en/language.types.string.php#language.types.string.syntax.double
     */
    public static function addSlashes($str) {
        return preg_replace_callback('#[^\x20-\x7E]|[\\\\"$]#', function ($m) {
            switch($m[0]) {
                case "\n": return '\n';
                case "\r": return '\r';
                case "\t": return '\t';
                case "\x0B": return PHP_VERSION_ID >= 50205 ? '\v' : '\x0B';
                case "\x1B": return PHP_VERSION_ID >= 50404 ? '\e' : '\x1B';
                case "\x0C": return PHP_VERSION_ID >= 50205 ? '\f' : '\x0C';
                case '$': return '\$';
                case '"': return '\\"';
                case "\0": return '\x00'; // can't export as \0 because if the next character is a digit 0-7, this will be misinterpreted!
                case '\\': return '\\\\';
            }
            return '\\x' . strtoupper(bin2hex($m[0]));
        }, $str);
    }

    /**
     * Interpret backslash escape sequences the way PHP does inside a double-quoted string.
     *
     * @param string $str
     * @return string
     * @see http://php.net/manual/en/language.types.string.php#language.types.string.syntax.double
     */
    public static function interpretDoubleQuotedString($str) {
        return preg_replace_callback('#\\\\([nrtvef\\\\$"]|[0-7]{1,3}|x[0-9A-Fa-f]{1,2})#', function($m) {
            switch($m[1]) {
                case 'n': return "\n";
                case 'r': return "\r";
                case 't': return "\t";
                case 'v': return "\x0B"; // since PHP 5.2.5
                case 'e': return "\x1B"; // since PHP 5.4.4
                case 'f': return "\x0C"; // since PHP 5.2.5
                case '\\': return '\\';
                case '$': return '$';
                case '"': return '"';
            }
            if($m[1][0] === 'x') {
                return hex2bin(substr($m[1],1));
            }
            return chr(octdec($m[1]));
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
        return '"'.self::addSlashes($str).'"';
    }

    /**
     * Interpret backslash escape sequences the way PHP does inside a single-quoted string.
     *
     * @param string $str
     * @return string
     * @see http://php.net/manual/en/language.types.string.php#language.types.string.syntax.single
     */
    public static function interpretSingleQuotedString($str) {
        return self::replace(['\\\\'=>'\\','\\\''=>"'"],$str);
    }

    public static function import($str) {
        if(strlen($str) < 2) {
            throw new ArgumentFormatException('str',"String must be wrapped in quotes");
        }
        $end = $str[strlen($str)-1];
        $inner = self::substrLen($str,1,-1);
        if($str[0] === '"' && $end === '"') {
            return self::interpretDoubleQuotedString($inner);
        }
        if($str[0] === "'" && $end === "'") {
            return self::interpretSingleQuotedString($inner);
        }
        throw new ArgumentFormatException('str',"String must be wrapped in single or double quotes");
    }

    /**
     * Adds quotes around a string.
     *
     * @param string $string String to add quotes to.
     * @param string $lquo Left quote
     * @param string|null $rquo Right quote. Defaults to same as left.
     * @return string
     */
    public static function quote($string, $lquo = '"', $rquo = null) {
        if($rquo === null) $rquo = $lquo;
        return $lquo.$string.$rquo;
    }

    /**
     * Removes surrounding quotes from a string, if they exist.
     *
     * @param string $string String to remove quotes from.
     * @param string $lquo Left quote
     * @param string|null $rquo Right quote. Defaults to same as left.
     * @return string
     */
    public static function unquote($string, $lquo = '"', $rquo = null) {
        if($rquo === null) $rquo = $lquo;
        $startLen = strlen($lquo);
        $endLen = strlen($rquo);
        if(strlen($string) >= ($startLen + $endLen) && self::startsWith($string,$lquo) && self::endsWith($string,$rquo)) {
            return substr($string, $startLen, -$endLen);
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

    /**
     * Checks to see if a string is utf8 encoded.
     *
     * NOTE: This function checks for 5-Byte sequences, UTF8
     *       has Bytes Sequences with a maximum length of 4.
     *
     * @author bmorel@ssi.fr
     *
     * @param string $str The string to be checked
     * @return bool True if $str fits a UTF-8 model, false otherwise.
     */
    private static function seemsUtf8($str) {
        $length = self::length($str);
        for($i = 0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if($c < 0x80) $n = 0; // 0bbbbbbb
            elseif(($c & 0xE0) == 0xC0) $n = 1; // 110bbbbb
            elseif(($c & 0xF0) == 0xE0) $n = 2; // 1110bbbb
            elseif(($c & 0xF8) == 0xF0) $n = 3; // 11110bbb
            elseif(($c & 0xFC) == 0xF8) $n = 4; // 111110bb
            elseif(($c & 0xFE) == 0xFC) $n = 5; // 1111110b
            else return false; // Does not match any model
            for($j = 0; $j < $n; $j++) { // n bytes matching 10bbbbbb follow ?
                if((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;
            }
        }
        return true;
    }


    /**
     * Removes diacritics (accents) from a string.
     *
     * @param string $string Text that might have accented characters
     * @return string Filtered string with replaced "nice" characters.
     * @credit https://core.trac.wordpress.org/browser/tags/3.9/src/wp-includes/formatting.php#L682
     */
    public static function removeDiacritics($string) {
        if(!preg_match('/[\x80-\xff]/', $string))
            return $string;

        if(self::seemsUtf8($string)) {
            $chars = [
                // Decompositions for Latin-1 Supplement
                chr(194) . chr(170) => 'a', chr(194) . chr(186) => 'o',
                chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
                chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
                chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
                chr(195) . chr(134) => 'AE', chr(195) . chr(135) => 'C',
                chr(195) . chr(136) => 'E', chr(195) . chr(137) => 'E',
                chr(195) . chr(138) => 'E', chr(195) . chr(139) => 'E',
                chr(195) . chr(140) => 'I', chr(195) . chr(141) => 'I',
                chr(195) . chr(142) => 'I', chr(195) . chr(143) => 'I',
                chr(195) . chr(144) => 'D', chr(195) . chr(145) => 'N',
                chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
                chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
                chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
                chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
                chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
                chr(195) . chr(158) => 'TH', chr(195) . chr(159) => 's',
                chr(195) . chr(160) => 'a', chr(195) . chr(161) => 'a',
                chr(195) . chr(162) => 'a', chr(195) . chr(163) => 'a',
                chr(195) . chr(164) => 'a', chr(195) . chr(165) => 'a',
                chr(195) . chr(166) => 'ae', chr(195) . chr(167) => 'c',
                chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
                chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
                chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
                chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
                chr(195) . chr(176) => 'd', chr(195) . chr(177) => 'n',
                chr(195) . chr(178) => 'o', chr(195) . chr(179) => 'o',
                chr(195) . chr(180) => 'o', chr(195) . chr(181) => 'o',
                chr(195) . chr(182) => 'o', chr(195) . chr(184) => 'o',
                chr(195) . chr(185) => 'u', chr(195) . chr(186) => 'u',
                chr(195) . chr(187) => 'u', chr(195) . chr(188) => 'u',
                chr(195) . chr(189) => 'y', chr(195) . chr(190) => 'th',
                chr(195) . chr(191) => 'y', chr(195) . chr(152) => 'O',
                // Decompositions for Latin Extended-A
                chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
                chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
                chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
                chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
                chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
                chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
                chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
                chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
                chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
                chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
                chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
                chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
                chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
                chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
                chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
                chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
                chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
                chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
                chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
                chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
                chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
                chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
                chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
                chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
                chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
                chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
                chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
                chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
                chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
                chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
                chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
                chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
                chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
                chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
                chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
                chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
                chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
                chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
                chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
                chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
                chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
                chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
                chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
                chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
                chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
                chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
                chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
                chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
                chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
                chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
                chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
                chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
                chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
                chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
                chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
                chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
                chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
                chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
                chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
                chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
                chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
                chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
                chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
                chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
                // Decompositions for Latin Extended-B
                chr(200) . chr(152) => 'S', chr(200) . chr(153) => 's',
                chr(200) . chr(154) => 'T', chr(200) . chr(155) => 't',
                // Euro Sign
                chr(226) . chr(130) . chr(172) => 'E',
                // GBP (Pound) Sign
                chr(194) . chr(163) => '',
                // Vowels with diacritic (Vietnamese)
                // unmarked
                chr(198) . chr(160) => 'O', chr(198) . chr(161) => 'o',
                chr(198) . chr(175) => 'U', chr(198) . chr(176) => 'u',
                // grave accent
                chr(225) . chr(186) . chr(166) => 'A', chr(225) . chr(186) . chr(167) => 'a',
                chr(225) . chr(186) . chr(176) => 'A', chr(225) . chr(186) . chr(177) => 'a',
                chr(225) . chr(187) . chr(128) => 'E', chr(225) . chr(187) . chr(129) => 'e',
                chr(225) . chr(187) . chr(146) => 'O', chr(225) . chr(187) . chr(147) => 'o',
                chr(225) . chr(187) . chr(156) => 'O', chr(225) . chr(187) . chr(157) => 'o',
                chr(225) . chr(187) . chr(170) => 'U', chr(225) . chr(187) . chr(171) => 'u',
                chr(225) . chr(187) . chr(178) => 'Y', chr(225) . chr(187) . chr(179) => 'y',
                // hook
                chr(225) . chr(186) . chr(162) => 'A', chr(225) . chr(186) . chr(163) => 'a',
                chr(225) . chr(186) . chr(168) => 'A', chr(225) . chr(186) . chr(169) => 'a',
                chr(225) . chr(186) . chr(178) => 'A', chr(225) . chr(186) . chr(179) => 'a',
                chr(225) . chr(186) . chr(186) => 'E', chr(225) . chr(186) . chr(187) => 'e',
                chr(225) . chr(187) . chr(130) => 'E', chr(225) . chr(187) . chr(131) => 'e',
                chr(225) . chr(187) . chr(136) => 'I', chr(225) . chr(187) . chr(137) => 'i',
                chr(225) . chr(187) . chr(142) => 'O', chr(225) . chr(187) . chr(143) => 'o',
                chr(225) . chr(187) . chr(148) => 'O', chr(225) . chr(187) . chr(149) => 'o',
                chr(225) . chr(187) . chr(158) => 'O', chr(225) . chr(187) . chr(159) => 'o',
                chr(225) . chr(187) . chr(166) => 'U', chr(225) . chr(187) . chr(167) => 'u',
                chr(225) . chr(187) . chr(172) => 'U', chr(225) . chr(187) . chr(173) => 'u',
                chr(225) . chr(187) . chr(182) => 'Y', chr(225) . chr(187) . chr(183) => 'y',
                // tilde
                chr(225) . chr(186) . chr(170) => 'A', chr(225) . chr(186) . chr(171) => 'a',
                chr(225) . chr(186) . chr(180) => 'A', chr(225) . chr(186) . chr(181) => 'a',
                chr(225) . chr(186) . chr(188) => 'E', chr(225) . chr(186) . chr(189) => 'e',
                chr(225) . chr(187) . chr(132) => 'E', chr(225) . chr(187) . chr(133) => 'e',
                chr(225) . chr(187) . chr(150) => 'O', chr(225) . chr(187) . chr(151) => 'o',
                chr(225) . chr(187) . chr(160) => 'O', chr(225) . chr(187) . chr(161) => 'o',
                chr(225) . chr(187) . chr(174) => 'U', chr(225) . chr(187) . chr(175) => 'u',
                chr(225) . chr(187) . chr(184) => 'Y', chr(225) . chr(187) . chr(185) => 'y',
                // acute accent
                chr(225) . chr(186) . chr(164) => 'A', chr(225) . chr(186) . chr(165) => 'a',
                chr(225) . chr(186) . chr(174) => 'A', chr(225) . chr(186) . chr(175) => 'a',
                chr(225) . chr(186) . chr(190) => 'E', chr(225) . chr(186) . chr(191) => 'e',
                chr(225) . chr(187) . chr(144) => 'O', chr(225) . chr(187) . chr(145) => 'o',
                chr(225) . chr(187) . chr(154) => 'O', chr(225) . chr(187) . chr(155) => 'o',
                chr(225) . chr(187) . chr(168) => 'U', chr(225) . chr(187) . chr(169) => 'u',
                // dot below
                chr(225) . chr(186) . chr(160) => 'A', chr(225) . chr(186) . chr(161) => 'a',
                chr(225) . chr(186) . chr(172) => 'A', chr(225) . chr(186) . chr(173) => 'a',
                chr(225) . chr(186) . chr(182) => 'A', chr(225) . chr(186) . chr(183) => 'a',
                chr(225) . chr(186) . chr(184) => 'E', chr(225) . chr(186) . chr(185) => 'e',
                chr(225) . chr(187) . chr(134) => 'E', chr(225) . chr(187) . chr(135) => 'e',
                chr(225) . chr(187) . chr(138) => 'I', chr(225) . chr(187) . chr(139) => 'i',
                chr(225) . chr(187) . chr(140) => 'O', chr(225) . chr(187) . chr(141) => 'o',
                chr(225) . chr(187) . chr(152) => 'O', chr(225) . chr(187) . chr(153) => 'o',
                chr(225) . chr(187) . chr(162) => 'O', chr(225) . chr(187) . chr(163) => 'o',
                chr(225) . chr(187) . chr(164) => 'U', chr(225) . chr(187) . chr(165) => 'u',
                chr(225) . chr(187) . chr(176) => 'U', chr(225) . chr(187) . chr(177) => 'u',
                chr(225) . chr(187) . chr(180) => 'Y', chr(225) . chr(187) . chr(181) => 'y',
                // Vowels with diacritic (Chinese, Hanyu Pinyin)
                chr(201) . chr(145) => 'a',
                // macron
                chr(199) . chr(149) => 'U', chr(199) . chr(150) => 'u',
                // acute accent
                chr(199) . chr(151) => 'U', chr(199) . chr(152) => 'u',
                // caron
                chr(199) . chr(141) => 'A', chr(199) . chr(142) => 'a',
                chr(199) . chr(143) => 'I', chr(199) . chr(144) => 'i',
                chr(199) . chr(145) => 'O', chr(199) . chr(146) => 'o',
                chr(199) . chr(147) => 'U', chr(199) . chr(148) => 'u',
                chr(199) . chr(153) => 'U', chr(199) . chr(154) => 'u',
                // grave accent
                chr(199) . chr(155) => 'U', chr(199) . chr(156) => 'u',
            ];

            // Used for locale-specific rules
            $locale = self::getLocale();

            if('de_DE' == $locale) {
                $chars[chr(195) . chr(132)] = 'Ae';
                $chars[chr(195) . chr(164)] = 'ae';
                $chars[chr(195) . chr(150)] = 'Oe';
                $chars[chr(195) . chr(182)] = 'oe';
                $chars[chr(195) . chr(156)] = 'Ue';
                $chars[chr(195) . chr(188)] = 'ue';
                $chars[chr(195) . chr(159)] = 'ss';
            } elseif('da_DK' === $locale) {
                $chars[chr(195) . chr(134)] = 'Ae';
                $chars[chr(195) . chr(166)] = 'ae';
                $chars[chr(195) . chr(152)] = 'Oe';
                $chars[chr(195) . chr(184)] = 'oe';
                $chars[chr(195) . chr(133)] = 'Aa';
                $chars[chr(195) . chr(165)] = 'aa';
            }

            $string = strtr($string, $chars);
        } else {
            $chars = [];
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
                . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
                . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
                . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
                . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
                . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
                . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
                . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
                . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
                . chr(252) . chr(253) . chr(255);

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars = [];
            $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $double_chars['out'] = ['OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th'];
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

        return $string;
    }

    private static function getLocale() {
        $locale = function_exists('locale_get_default') ? locale_get_default() : null;
        return $locale ?: ini_get('intl.default_locale') ?: 'en_US';
    }

    /**
     * Splits a programmer's variable name into words.
     *
     * @param string $str
     * @param string $encoding
     * @return array
     */
    protected static function splitCodeWords($str, $encoding=null) {
        if($encoding === null) $encoding = mb_internal_encoding();
        $str = str_replace("'",'',$str); // strip apostrophes
        $str = preg_replace_callback('~\p{Lu}+~u',function($m) use ($encoding) {
            $w = mb_strtolower($m[0], $encoding);
            return ' ' . (mb_strlen($w, $encoding) > 1 ? mb_substr($w, 0, -1, $encoding) . ' ' . mb_substr($w, -1, null, $encoding) : $w);
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

    /**
     * Splits a string, trims whitespace, and removes empty elements. Supports CSV format in case your args start/end with whitespace and/or contain a comma.
     *
     * Useful for parsing user-submitted text into an array.
     *
     * @param string $subject
     * @param string $delimiter
     * @param string $enclosure
     * @return array
     */
    public static function smartSplit($subject, $delimiter = ',', $enclosure = '"') {
        $out = [];
        $delimLen = strlen($delimiter);
        $encLen = strlen($enclosure);
        $subjectLen = strlen($subject);
        $end = $subjectLen - $delimLen;
        $start = 0;
        $inQuotes = false;
        for($i=0; $i<=$end;) {
            if(self::contains($subject,$enclosure,false,$i)) { // todo: how to handle escaping of quotes??
                $inQuotes = !$inQuotes;
                $i += $encLen;
            } elseif(!$inQuotes && self::contains($subject,$delimiter,false,$i)) {
                $frag = self::unquote(trim(substr($subject, $start, $i - $start)), $enclosure); // todo: implement self::import and use that instead
                if($frag !==  '') $out[] = $frag;
                $i += $delimLen;
                $start = $i;
            } else {
                ++$i;
            }
        }
        if($start < $subjectLen) {
            $frag = self::unquote(trim(substr($subject, $start)), $enclosure);
            if($frag !==  '') $out[] = $frag;
        }
        return $out;
    }
}
