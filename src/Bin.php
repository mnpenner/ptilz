<?php
namespace Ptilz;

use Ptilz\BigMath;
use Ptilz\Exceptions\ArgumentException;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Exceptions\NotImplementedException;
use Ptilz\Exceptions\NotSupportedException;
use SebastianBergmann\Exporter\Exception;

/**
 * Functions for working with binary data
 */
abstract class Bin {
    /** @var bool */
    private static $_isLittleEndian = null;

    /**
     * Determines if the machine is Little Endian; i.e., the least significant byte (LSB) comes first
     * @return bool
     */
    public static function isLittleEndian() {
        if(self::$_isLittleEndian === null) self::$_isLittleEndian = pack('S', 17740) === 'LE'; // unpack('v', 'LE')[1] === 17740
        return self::$_isLittleEndian;
    }

    /**
     * Determines if the machine is Big Endian; i.e., the most significant byte (MSB) comes first
     * @return bool
     */
    public static function isBigEndian() {
        return !self::isLittleEndian();
    }


    /**
     * @param string|array $format
     * @param string $data Packed binary data
     * @param int $offset Starting offset in bytes. Will be updated as data is read.
     * @return array|mixed
     * @throws Exceptions\ArgumentException
     * @throws Exceptions\ArgumentTypeException
     * @throws Exceptions\InvalidOperationException
     * @throws Exceptions\NotImplementedException
     */
    public static function unpack($format, $data, &$offset = 0) {
        if(is_string($format)) {
            $format = [$format];
            $singleValue = true;
        } elseif(is_array($format)) {
            $singleValue = false;
        } else {
            throw new ArgumentTypeException('format', 'string|array');
        }

        $out = [];

        // swap machine-dependent sizes/encodings for fixed types
        foreach($format as $key => $fullType) {
            $m = self::matchType($fullType);
            if($m === false) throw new ArgumentException('format', "`$key` has an unrecognized type '$fullType'");

            $type = self::getFixedType($m['type']);
            $repeat = (int)Arr::get($m, 'repeat', '1');

            // unpack the data
            switch($type) {
                case 'char':
                    $out[$key] = self::_unpack($offset,'c',$repeat,$data);
                    $offset += $repeat;
                    break;
                case 'byte':
                    $out[$key] = self::_unpack($offset,'C',$repeat,$data);
                    $offset += $repeat;
                    break;
                case '+uint16':
                    $out[$key] = self::_unpack($offset,'n',$repeat,$data);
                    $offset += 2*$repeat;
                    break;
                case '-uint16':
                    $out[$key] = self::_unpack($offset,'v',$repeat,$data);
                    $offset += 2*$repeat;
                    break;
                case '+uint32':
                    $out[$key] = self::_unpack($offset,'N',$repeat,$data);
                    $offset += 4*$repeat;
                    break;
                case '-uint32':
                    $out[$key] = self::_unpack($offset,'V',$repeat,$data);
                    $offset += 4*$repeat;
                    break;
                case 'float32':
                    $out[$key] = self::_unpack($offset,'f',$repeat,$data);
                    $offset += 4*$repeat;
                    break;
                case 'float64':
                    $out[$key] = self::_unpack($offset,'d',$repeat,$data);
                    $offset += 8*$repeat;
                    break;
                case '-uint48':
                    if($repeat === 1) $out[$key] = [];
                    for($i = 0; $i < $repeat; ++$i) {
                        $ints = unpack("@$offset/Vlo/vhi", $data);
                        $sum = BigMath::add($ints['lo'], BigMath::mul($ints['hi'], '4294967296'));
                        if($repeat === 1) $out[$key] = $sum;
                        else $out[$key][] = $sum;
                        $offset += 8;
                    }
                    break;
                case '+uint48':
                    if($repeat === 1) $out[$key] = [];
                    for($i = 0; $i < $repeat; ++$i) {
                        $ints = unpack("@$offset/nhi/Nlo", $data);
                        $sum = BigMath::add($ints['lo'], BigMath::mul($ints['hi'], '4294967296'));
                        if($repeat === 1) $out[$key] = $sum;
                        else $out[$key][] = $sum;
                        $offset += 8;
                    }
                    break;
                case '-uint64':
                    if($repeat === 1) $out[$key] = [];
                    for($i = 0; $i < $repeat; ++$i) {
                        $ints = unpack("@$offset/Vlo/Vhi", $data);
                        $sum = BigMath::add($ints['lo'], BigMath::mul($ints['hi'], '4294967296'));
                        if($repeat === 1) $out[$key] = $sum;
                        else $out[$key][] = $sum;
                        $offset += 8;
                    }
                    break;
                case '+uint64':
                    if($repeat === 1) $out[$key] = [];
                    for($i = 0; $i < $repeat; ++$i) {
                        $ints = unpack("@$offset/Nhi/Nlo", $data);
                        $sum = BigMath::add($ints['lo'], BigMath::mul($ints['hi'], '4294967296'));
                        if($repeat === 1) $out[$key] = $sum;
                        else $out[$key][] = $sum;
                        $offset += 8;
                    }
                    break;
                case 'str':
                    if($repeat === 1) $out[$key] = [];
                    for($i=0; $i<$repeat; ++$i) {
                        if(preg_match('~\d+\z~A', $m['len'])) {
                            $strlen = (int)$m['len'];
                        } else {
                            if(!array_key_exists($m['len'], $out)) {
                                throw new ArgumentException("Length value '$m[len]' not found for `$key`");
                            }
                            $strlen = $out[$m['len']];
                        }
                        $str = substr($data, $offset, $strlen);
                        if($repeat === 1) $out[$key] = $str;
                        else $out[$key][] = $str;
                        $offset += $strlen;
                    }
                    break;
                case '@':
                    if($m['len'][0] === '-' || $m['len'][0] === '+') {
                        $offset += (int)$m['len'];
                    } else {
                        $offset = (int)$m['len'];
                    }
                    break;
                default:
                    throw new NotImplementedException("Type '$type' has not been implemented yet");
            }
        }
        return $singleValue ? reset($out) : $out;
    }

    private static function _unpack($offset, $format, $repeat, $data) {
        $result = unpack("@$offset/$format$repeat", $data);
        return $repeat == 1 ? $result[1] : array_values($result);
    }

    private static function getFixedType($type) {
        switch($type) {
            // see http://stackoverflow.com/q/24785801/65387
            case 'int':
                return self::getFixedType('int'.(PHP_INT_SIZE*8));
            case 'uint':
                return self::getFixedType('uint'.(PHP_INT_SIZE*8));
            case 'int16':
                return self::isLittleEndian() ? '-int16' : '+int16';
            case 'uint16':
                return self::isLittleEndian() ? '-uint16' : '+uint16';
            case 'int32':
                return self::isLittleEndian() ? '-int32' : '+int32';
            case 'uint32':
                return self::isLittleEndian() ? '-uint32' : '+uint32';
            case 'int48':
                return self::isLittleEndian() ? '-int48' : '+int48';
            case 'uint48':
                return self::isLittleEndian() ? '-uint48' : '+uint48';
            case 'int64':
                return self::isLittleEndian() ? '-int64' : '+int64';
            case 'uint64':
                return self::isLittleEndian() ? '-uint64' : '+uint64';
            default:
                return $type;
        }
    }

    /**
     * @param string $type
     * @return bool|array
     */
    private static function matchType($type) {
        $patt = '~
            (?|
                (?<type>
                      char
                    | byte
                    | u?int
                    | [-+]?u?int(?:16|32|48|64)
                    | float(?:32|64)
                )
                | (?<type>str) (?:\[ (?<len>[^\]]+) \])?
                | (?<type>@) (?<len>[-+]?\d+)
            )
            (?: \{(?<repeat>\d+)\} )?
            \z~Amsx';

        return preg_match($patt, $type, $matches) ? $matches : false;
    }


    public static function pack($format, $args) {
        if(is_string($format)) {
            $format = [$format];
        } elseif(!is_array($format)) {
            throw new ArgumentTypeException('format', 'string|array');
        }

        $out = '';

        if(is_array($args)) {
            $args = Arr::flatten($args);
        } else {
            $args = [$args];
        }
        $idx = 0;
        foreach($format as $key => $fullType) {
            $m = self::matchType($fullType);
            if($m === false) throw new ArgumentException('format', "`$key` has an unrecognized type '$fullType'");;

            $type = self::getFixedType($m['type']);
            $repeat = (int)Arr::get($m, 'repeat', '1');

            for($_=0; $_<$repeat; ++$_) {
                switch($type) {
                    case 'char':
                        $out .= pack('c', $args[$idx]);
                        break;
                    case 'byte':
                        $out .= pack('C', $args[$idx]);
                        break;
                    case '+uint16':
                        $out .= pack('n', $args[$idx]);
                        break;
                    case '-uint16':
                        $out .= pack('v', $args[$idx]);
                        break;
                    case '+uint32':
                        $out .= pack('N', $args[$idx]);
                        break;
                    case '-uint32':
                        $out .= pack('V', $args[$idx]);
                        break;
                    case '+uint48':
                        $out .= pack('nN', (int)bcdiv($args[$idx],'4294967296'), (int)bcmod($args[$idx],'4294967296'));
                        break;
                    case '-uint48':
                        $out .= pack('Vv', (int)bcmod($args[$idx],'4294967296'), (int)bcdiv($args[$idx],'4294967296'));
                        break;
                    case '+uint64':
                        // if(PHP_VERSION_ID >= 50603) {
                        //     $out .= pack('J', $args[$idx]);
                        // } else {
                            $out .= pack('NN', (int)bcdiv($args[$idx], '4294967296'), (int)bcmod($args[$idx], '4294967296'));
                        // }
                        break;
                    case '-uint64':
                        // if(PHP_VERSION_ID >= 50603) {
                        //     $out .= pack('P', $args[$idx]);
                        // } else {
                            $out .= pack('VV', (int)bcmod($args[$idx], '4294967296'), (int)bcdiv($args[$idx], '4294967296'));
                        // }
                        break;
                    case 'float32':
                        $out .= pack('f', $args[$idx]);
                        break;
                    case 'float64':
                        $out .= pack('d', $args[$idx]);
                        break;
                    case 'str':
                        $formatStr = 'a';
                        $lenStr = Arr::get($m, 'len', '');
                        if($lenStr !== '') {
                            if(!Str::isInt($lenStr)) throw new ArgumentException("Length must be an integer or omitted for format argument $idx (got '$lenStr')");
                            $formatStr .= $lenStr;
                        } else {
                            $formatStr .= strlen($args[$idx]);
                        }
                        $out .= pack($formatStr, $args[$idx]);
                        break;
                    default:
                        throw new NotImplementedException("Type '$m[type]' has not been implemented yet");
                }
                ++$idx;
            }
        }

        return $out;
    }

    /**
     * Returns the length of a string in bytes. Immune to `mbstring.func_overload`.
     *
     * @param $bin
     * @return int
     */
    public static function length($bin) {
        return function_exists('mb_strlen') ? mb_strlen($bin, '8bit') : strlen($bin);
    }

    /**
     * Determines if an integer value contains a flag, i.e., has that bit set.
     *
     * @param int $flags Integer value
     * @param int $bit Bit(s) to check
     * @return bool
     */
    public static function hasFlag($flags, $bit) {
        return ($flags & $bit) === $bit;
    }

    /**
     * Set or clear a bit.
     *
     * @param int $int Input number.
     * @param int $bit Bit to set.
     * @param int $state Value to set bit to: 0 or 1.
     * @return int
     */
    public static function setBit($int, $bit, $state = 1) {
        return $state
            ? $int | (1 << $bit)
            : $int & ~(1 << $bit);
    }

    /**
     * Generate a pseudo-random string of bytes
     *
     * @param int $length
     * @return string The generated string of bytes
     * @throws Exceptions\NotSupportedException
     * @deprecated
     */
    public static function random($length) {
        return self::secureRandomBytes($length);
    }

    /**
     * Generates a string of pseudo-random bytes, with the number of bytes determined by the length parameter.
     *
     * @param int $length The length of the random string that should be returned in bytes.
     * @return string A string containing the requested number of cryptographically secure random bytes.
     * @throws NotSupportedException
     */
    public static function secureRandomBytes($length) {
        if($length <= 0) {
            throw new ArgumentOutOfRangeException('length',"Length must be positive");
        }

        if(function_exists('random_bytes')) {
            return random_bytes($length);
        }

        if(function_exists('openssl_random_pseudo_bytes') && PHP_VERSION_ID >= 50304) {
            $data = openssl_random_pseudo_bytes($length, $strong);
            if($strong) {
                return $data;
            }
        }

        if(function_exists('mcrypt_create_iv') && PHP_VERSION_ID >= 50307) {
            $data = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            if($data !== false) {
                return $data;
            }
        }

        $data = @file_get_contents('/dev/urandom', null, null, 0, $length);
        if($data !== false) {
            return $data;
        }

        throw new NotSupportedException("Your system does does not support any secure sources of randomness");
    }
}
