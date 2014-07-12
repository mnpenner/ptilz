<?php
namespace Ptilz;

/**
 * Mathematical functions
 */
abstract class Math {
    /**
     * Converts a base 10 number to any other base.
     *
     * @param int $val Decimal number
     * @param int $base Base to convert to. If null, will use strlen($chars) as base.
     * @param string $chars Characters used in base, arranged lowest to highest. Must be at least $base characters long.
     *
     * @return string    Number converted to specified base
     */
    public static function decToAnyBase($val, $base, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        if($base === null) $base = strlen($chars);
        $str = '';
        do {
            $m = bcmod($val, $base);
            $str = $chars[$m] . $str;
            $val = bcdiv(bcsub($val, $m), $base);
        } while(bccomp($val, 0) > 0);
        return $str;
    }

    /**
     * Convert a number from any base to base 10
     *
     * @param string $str Number
     * @param int $base Base of number. If null, will use strlen($chars) as base.
     * @param string $chars Characters use in base, arranged lowest to highest. Must be at least $base characters long.
     *
     * @return int|string    Number converted to base 10
     */
    public static function anyBaseToDec($str, $base, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        if($base === null) $base = strlen($chars);
        $len = strlen($str);
        $val = 0;
        $arr = array_flip(str_split($chars));
        for($i = 0; $i < $len; ++$i) {
            $val = bcadd($val, bcmul($arr[$str[$i]], bcpow($base, $len - $i - 1)));
        }
        return bccomp($val, PHP_INT_MIN) < 0 || bccomp($val, PHP_INT_MAX) > 0 ? (string)$val : (int)$val;
    }

    /**
     * Converts from one base to another.
     *
     * @param string $num
     * @param int    $fromBase
     * @param int    $toBase
     * @param string $chars
     * @return string
     */
    public static function changeBase($num, $fromBase, $toBase, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        return self::decToAnyBase(self::anyBaseToDec($num, $fromBase, $chars), $toBase, $chars);
    }

    /**
     * Clamps a number to be between two values.
     *
     * @param int|float $val
     * @param int|float $min
     * @param int|float $max
     * @return int|float
     */
    public static function clamp($val, $min, $max) {
        return min(max($val, $min), $max);
    }

    /**
     * Determines if a number is between two other numbers (inclusive). Supports arbitrary precision.
     *
     * @param int|string $n           Number to check
     * @param int|string $lower_bound Lower bound
     * @param int|string $upper_bound Upper bound
     * @param bool       $inclusive   If true, use >= and <=, else use > and <
     * @return bool $x is between $lower_bound and $upper_bound
     */
    public static function between($n, $lower_bound, $upper_bound, $inclusive = true) {
        return $inclusive
            ? bccomp($n, $lower_bound) >= 0 && bccomp($n, $upper_bound) <= 0
            : bccomp($n, $lower_bound) > 0 && bccomp($n, $upper_bound) < 0;
    }

    /**
     * Converts large hexidecimal numbers into decimal strings.
     *
     * @param string $hex Hexidecimal representation of number
     * @return string Decimal representation of number
     */
    public static function hexToDec($hex) {
        return self::anyBaseToDec(strtolower($hex), 16, '0123456789abcdef');
    }

    /**
     * @param int|string $dec Decimal representation of number
     * @param bool       $uppercase Use uppercase letters A-F
     * @return string Hexidecimal representation of number
     */
    public static function decToHex($dec, $uppercase=false) {
        return self::decToAnyBase($dec, 16, $uppercase ? '0123456789ABCDEF' : '0123456789abcdef');
    }

}