<?php
namespace Ptilz;

use Ptilz\Exceptions\ArgumentException;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Exceptions\InvalidOperationException;
use Ptilz\Exceptions\NotImplementedException;

/**
 * Functions for working with binary data
 */
abstract class Bin {
    /** @var string */
    private static $_unpackArgPrefix = "\036";
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
     * @param string|int $format
     * @param string $data Packed binary data
     * @param int $offset Starting offset in bytes. Will be updated as data is read.
     * @return array|mixed
     * @throws Exceptions\ArgumentException
     * @throws Exceptions\ArgumentTypeException
     * @throws Exceptions\InvalidOperationException
     * @throws Exceptions\NotImplementedException
     */
    public static function unpack($format, $data, &$offset = 0) {
        // TODO:
        // add repeaters * and {3} which should return an array

        if(is_string($format)) {
            $format = [$format];
            $singleValue = true;
        } elseif(is_array($format)) {
            $singleValue = false;
        } else {
            throw new ArgumentTypeException('format', 'string|array');
        }

        $out = [];

        $patt = '~
            (?|
                (?<type>
                      char
                    | byte
                    | u?int
                    | [-+]?u?int(?:16|32|64)
                    | float(?:32|64)
                )
                | (?<type>str) (?:\[ (?<len>[^\]]+) \])
                | (?<type>@) (?<len>[-+]?\d+)
            )\z
            ~Amsx';


        // swap machine-dependent sizes/encodings for fixed types
        foreach($format as $key => $fullType) {
            if(!preg_match($patt, $fullType, $m)) {
                throw new ArgumentException('format', "`$key` has an unrecognized type '$fullType'");
            }

            $type = self::getFixedType($m['type']);

            // unpack the data
            switch($type) {
                case 'char':
                    $out[$key] = unpack("@$offset/c",$data)[1];
                    ++$offset;
                    break;
                case 'byte':
                    $out[$key] = unpack("@$offset/C",$data)[1];
                    ++$offset;
                    break;
                case '+uint16':
                    $out[$key] = unpack("@$offset/n",$data)[1];
                    $offset += 2;
                    break;
                case '-uint16':
                    $out[$key] = unpack("@$offset/v",$data)[1];
                    $offset += 2;
                    break;
                case '+uint32':
                    $out[$key] = unpack("@$offset/N",$data)[1];
                    $offset += 4;
                    break;
                case '-uint32':
                    $out[$key] = unpack("@$offset/V",$data)[1];
                    $offset += 4;
                    break;
                case 'float32':
                    $out[$key] = unpack("@$offset/f",$data)[1];
                    $offset += 4;
                    break;
                case 'float64':
                    $out[$key] = unpack("@$offset/d",$data)[1];
                    $offset += 8;
                    break;
                case '-uint64':
                    $ints = unpack("@$offset/Vlo/Vhi", $data);
                    $out[$key] = Math::add($ints['lo'], Math::mul($ints['hi'], '4294967296'));
                    break;
                case '+uint64':
                    $ints = unpack("@$offset/Nhi/Nlo", $data);
                    $out[$key] = Math::add($ints['lo'], Math::mul($ints['hi'], '4294967296'));
                    break;
                case 'str':
                    if(preg_match('~\d+\z~A', $m['len'])) {
                        $strlen = (int)$m['len'];
                    } else {
                        if(!array_key_exists($m['len'], $out)) {
                            throw new ArgumentException("Length value '$m[len]' not found for `$key`");
                        }
                        $strlen = $out[$m['len']];
                    }
                    $out[$key] = unpack("@$offset/a$strlen",$data)[1];
                    $offset += $strlen;
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
            case 'int64':
                return self::isLittleEndian() ? '-int64' : '+int64';
            case 'uint64':
                return self::isLittleEndian() ? '-uint64' : '+uint64';
            default:
                return $type;
        }
    }


    public static function pack(array $format, array $args) {

        $patt = '~
            (?|
                (?<type>
                      char
                    | byte
                    | u?int
                    | [-+]?u?int(?:16|32|64)
                    | float(?:32|64)
                )
                | (?<type>str) (?:\[ (?<len>[^\]]+) \])?
                | (?<type>@) (?<len>[-+]?\d+)
            )
            (?: \{(?<repeat>\d+)\} )?
            \z
            ~Amsx';

        $out = '';
        $args = Arr::flatten($args);
        $idx = 0;
        $formatStr = '';
        foreach($format as $key => $fullType) {
            if(!preg_match($patt, $fullType, $m)) {
                throw new ArgumentException('format', "`$key` has an unrecognized type '$fullType'");
            }

            $type = self::getFixedType($m['type']);

            switch($type) {
                case 'char':
                    $out .= pack('c',$args[$idx]);
                    break;
                case 'byte':
                    $out .= pack('C',$args[$idx]);
                    break;
                case '+uint16':
                    $out .= pack('n',$args[$idx]);
                    break;
                case '-uint16':
                    $out .= pack('v',$args[$idx]);
                    break;
                case '+uint32':
                    $out .= pack('N',$args[$idx]);
                    break;
                case '-uint32':
                    $out .= pack('V',$args[$idx]);
                    break;
                case '+uint64':
                    throw new NotImplementedException();
                    break;
                case '-uint64':
                    throw new NotImplementedException();
                    break;
                case 'float32':
                    $out .= pack('f',$args[$idx]);
                    break;
                case 'float64':
                    $out .= pack('d',$args[$idx]);
                    break;
                case 'str':
                    $out .= pack('a',$args[$idx]);
                    $lenStr = Arr::get($m, 'len', '');
                    if($lenStr !== '') {
                        $out .= $lenStr;
                    } else {
                        $out .= strlen($args[$idx]);
                    }
                    break;
                default:
                    throw new NotImplementedException("Type '$m[type]' has not been implemented yet");
            }

//            $repeatStr = Arr::get($m, 'repeat', '');
//            if($repeatStr !== '') {
//                $repeatVal = (int)$repeatStr;
//                // fixme: strings need special attention...
//                if($m['type'] === 'str') throw new NotImplementedException("Cannot repeat strings; use multiple array args");
//                $formatStr .= $repeatVal;
//                $idx += $repeatVal;
//            } else {
//                ++$idx;
//            }
        }

//        array_unshift($args, $formatStr);
//        var_dump($args);exit;
//        return call_user_func_array('pack', $args);
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
     * @param int $val  Value
     * @param int $flag Flag
     * @return bool
     */
    public static function hasFlag($val, $flag) {
        return ($val & $flag) === $flag;
    }
}