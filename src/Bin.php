<?php
namespace Ptilz;

use Ptilz\Exceptions\ArgumentException;
use Ptilz\Exceptions\NotImplementedException;

/**
 * Functions for working with binary data
 */
abstract class Bin {
    private static $_unpackArgPrefix = "\036";

    public static function unpack(array $formatArray, $data, &$offset = 0) {
        // todo allow $formatArray to be a string and return a non-array (maybe return reset($result))
        // allow / in key by sending placeholder (like 'x') to pack() instead of the actual name
        // add repeaters * and {3} which should return an array
        $packArgs = [];
        if($offset !== 0) $packArgs[] = "@$offset";
        $result = [];
        $sizeOfInt = strlen(decbin(~0)) / 8; // not sure if there's a better way to find this out

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
                    | float
                    | double
                )
                | (?<type>str) (?:\[ (?<len>[^\]]+) \])
                | (?<type>@) (?<len>[-+]?\d+)
            )\z
            ~Amsx';

        $doStrip = false;

        foreach($formatArray as $key => $type) {
            if(!preg_match($patt, $type, $m)) {
                throw new ArgumentException('format', "`$key` has an unrecognized type '$type'");
            }

            switch($m['type']) {
                case 'char':
                    $formatStr = 'c';
                    ++$offset;
                    break;
                case 'byte':
                    $formatStr = 'C';
                    ++$offset;
                    break;
                case 'int':
                    $formatStr = 'i';
                    $offset += $sizeOfInt;
                    break;
                case 'uint':
                    $formatStr = 'I';
                    $offset += $sizeOfInt;
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
                case 'float':
                    $formatStr = 'f';
                    $offset += $sizeOfInt;
                    break;
                case 'double':
                    $formatStr = 'd';
                    $offset += 2 * $sizeOfInt;
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
                    throw new NotImplementedException("Type '$m[type]' has not been implemented yet");
            }

            if(is_int($key) || preg_match('~\d~A', $key)) {
                $key = self::$_unpackArgPrefix . $key;
                $doStrip = true;
            }

            $formatStr .= $key;
            $packArgs[] = $formatStr;
        }

        self::_doUnpack($result, $packArgs, $data, $doStrip);
        return $result;
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
     * @param int $val Value
     * @param int $flag Flag
     * @return bool
     */
    public static function hasFlag($val, $flag) {
        return ($val & $flag) === $flag;
    }
}