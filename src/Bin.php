<?php
namespace Ptilz;

use Ptilz\Exceptions\ArgumentException;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Exceptions\InvalidOperationException;
use Ptilz\Exceptions\NotImplementedException;
use Ptilz\Exceptions\NotSupportedException;

/**
 * Functions for working with binary data
 */
abstract class Bin {
    private static $_unpackArgPrefix = "\036";
    private static $_isLittleEndian = null;

    public static function isLittleEndian() {
        if(self::$_isLittleEndian === null) self::$_isLittleEndian = pack('S', 256) === "\x00\x01"; // in Little Endian, the least significant byte is on the left
        return self::$_isLittleEndian;
    }

    public static function isBigEndian() {
        return !self::isLittleEndian();
    }

    public static function unpack($format, $data, &$offset = 0) {
        // TODO:
        // allow / in key by sending placeholder (like 'x') to pack() instead of the actual name
        // add repeaters * and {3} which should return an array

        if(is_string($format)) {
            $format = [$format];
            $returnFirst = true;
        } elseif(is_array($format)) {
            $returnFirst = false;
        } else {
            throw new ArgumentTypeException('format', 'string|array');
        }

        $packArgs = [];
        if($offset !== 0) $packArgs[] = "@$offset";
        $result = [];

        $patt = '~
            (?|
                (?<type>
                      char
                    | byte
                    | int
                    | uint
                    | [-+]?int16
                    | [-+]?uint16
                    | [-+]?int32
                    | [-+]?uint32
                    | [-+]?int64
                    | [-+]?uint64
                    | float32
                    | float64
                )
                | (?<type>str) (?:\[ (?<len>[^\]]+) \])
                | (?<type>@) (?<len>[-+]?\d+)
            )\z
            ~Amsx';

        $doStrip = false;

        foreach($format as $key => $fullType) {
            if(!preg_match($patt, $fullType, $m)) {
                throw new ArgumentException('format', "`$key` has an unrecognized type '$fullType'");
            }

            switch($m['type']) {
                // see http://stackoverflow.com/q/24785801/65387
                case 'int':
                    switch(PHP_INT_SIZE) {
                        case 4:
                            $type = 'int32';
                            break;
                        case 8:
                            $type = 'int64';
                            break;
                        default:
                            throw new InvalidOperationException("Unexpected integer size: " . PHP_INT_SIZE);
                    }
                    break;
                case 'uint':
                    switch(PHP_INT_SIZE) {
                        case 4:
                            $type = 'uint32';
                            break;
                        case 8:
                            $type = 'uint64';
                            break;
                        default:
                            throw new InvalidOperationException("Unexpected integer size: " . PHP_INT_SIZE);
                    }
                    break;
                default:
                    $type = $m['type'];
                    break;
            }

            switch($type) {
                case 'char':
                    $formatStr = 'c';
                    ++$offset;
                    break;
                case 'byte':
                    $formatStr = 'C';
                    ++$offset;
                    break;
                case 'int16':
                    $formatStr = 's';
                    $offset += 2;
                    break;
                case 'uint16':
                    $formatStr = 'S';
                    $offset += 2;
                    break;
                case '+uint16':
                    $formatStr = 'n';
                    $offset += 2;
                    break;
                case '-uint16':
                    $formatStr = 'v';
                    $offset += 2;
                    break;
                case 'int32':
                    $formatStr = 'l';
                    $offset += 4;
                    break;
                case 'uint32':
                    $formatStr = 'L';
                    $offset += 4;
                    break;
                case '+uint32':
                    $formatStr = 'N';
                    $offset += 4;
                    break;
                case '-uint32':
                    $formatStr = 'V';
                    $offset += 4;
                    break;
                case 'float32':
                    $formatStr = 'f';
                    $offset += 4;
                    break;
                case 'float64':
                    $formatStr = 'd';
                    $offset += 8;
                    break;
                case 'str':
                    if(preg_match('~\d+\z~A', $m['len'])) {
                        $strlen = (int)$m['len'];
                    } else {
                        self::_doUnpack($result, $packArgs, $data, $doStrip);
                        $packArgs = ["@$offset"];
                        if(!array_key_exists($m['len'], $result)) {
                            throw new ArgumentException("Length value '$m[len]' not found for `$key`");
                        }
                        $strlen = $result[$m['len']];
                    }
                    $formatStr = 'a' . $strlen;
                    $offset += $strlen;
                    break;
                case '@':
                    if($m['len'][0] === '-' || $m['len'][0] === '+') {
                        $offset += (int)$m['len'];
                    } else {
                        $offset = (int)$m['len'];
                    }
                    $packArgs[] = "@$offset";
                    continue 2; // we don't want to add a name to this, skip over the junk below
                default:
                    throw new NotImplementedException("Type '$type' has not been implemented yet");
            }

            if(is_int($key) || preg_match('~\d~A', $key)) {
                $key = self::$_unpackArgPrefix . $key;
                $doStrip = true;
            }

            $formatStr .= $key;
            $packArgs[] = $formatStr;
        }

        self::_doUnpack($result, $packArgs, $data, $doStrip);
        return $returnFirst ? reset($result) : $result;
    }


    private static function _doUnpack(&$result, $packArgs, $binStr, &$doStrip) {
        $formatStr = implode('/', $packArgs);
//        echo "FORMAT: $packFormatStr\n";
        $unpacked = unpack($formatStr, $binStr);
        if($doStrip) {
            $unpacked = Arr::removeKeyPrefix($unpacked, self::$_unpackArgPrefix, false);
            $doStrip = false;
        }
        Arr::extend($result, $unpacked);
    }

    public static function pack(array $format, array $args) {

        $patt = '~
            (?|
                (?<type>
                      char
                    | byte
                    | int
                    | uint
                    | [-+]?int16
                    | [-+]?uint16
                    | [-+]?int32
                    | [-+]?uint32
                    | [-+]?int64
                    | [-+]?uint64
                    | float32
                    | float64
                )
                | (?<type>str) (?:\[ (?<len>[^\]]+) \])?
                | (?<type>@) (?<len>[-+]?\d+)
            )
            (?: \{(?<repeat>\d+)\} )?
            \z
            ~Amsx';

        $idx = 0;
        $formatStr = '';
        foreach($format as $key => $fullType) {
            if(!preg_match($patt, $fullType, $m)) {
                throw new ArgumentException('format', "`$key` has an unrecognized type '$fullType'");
            }


            switch($m['type']) {
                case 'char':
                    $formatStr .= 'c';
                    break;
                case 'byte':
                    $formatStr .= 'C';
                    break;
                case 'int':
                    $formatStr .= 'i';
                    break;
                case 'uint':
                    $formatStr .= 'I';
                    break;
                case 'int16':
                    $formatStr .= 's';
                    break;
                case 'uint16':
                    $formatStr .= 'S';
                    break;
                case '+uint16':
                    $formatStr .= 'n';
                    break;
                case '-uint16':
                    $formatStr .= 'v';
                    break;
                case 'int32':
                    $formatStr .= 'l';
                    break;
                case 'uint32':
                    $formatStr .= 'L';
                    break;
                case '+uint32':
                    $formatStr .= 'N';
                    break;
                case '-uint32':
                    $formatStr .= 'V';
                    break;
                case 'int64':
                    throw new NotImplementedException();
                    break;
                case 'uint64':
                    throw new NotImplementedException();
                    break;
                case '+uint64':
                    throw new NotImplementedException();
                    break;
                case '-uint64':
                    throw new NotImplementedException();
                    break;
                case 'float32':
                    $formatStr .= 'f';
                    break;
                case 'float64':
                    $formatStr .= 'd';
                    break;
                case 'str':
                    $formatStr .= 'a';
                    $lenStr = Arr::get($m, 'len', '');
                    if($lenStr !== '') {
                        $formatStr .= $lenStr;
                    } else {
                        $formatStr .= strlen($args[$idx]);
                    }
                    break;
                default:
                    throw new NotImplementedException("Type '$m[type]' has not been implemented yet");
            }

            $repeatStr = Arr::get($m, 'repeat', '');
            if($repeatStr !== '') {
                $repeatVal = (int)$repeatStr;
                // fixme: strings need special attention...
                if($m['type'] === 'str') throw new NotImplementedException("Cannot repeat strings; use multiple array args");
                $formatStr .= $repeatVal;
                $idx += $repeatVal;
            } else {
                ++$idx;
            }
        }

        array_unshift($args, $formatStr);
//        var_dump($args);exit;
        return call_user_func_array('pack', $args);
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