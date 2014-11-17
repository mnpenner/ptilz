<?php
namespace Ptilz;
use Ptilz\BigMath;
use Ptilz\Exceptions\ArgumentEmptyException;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Exceptions\InvalidOperationException;

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
        return BigMath::toInt($val);
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

    /**
     * Discard the lowest and highest X% of values then take the average.
     *
     * @param int[]|float[] $values
     * @param $percent
     * @throws Exceptions\ArgumentEmptyException
     * @throws Exceptions\ArgumentOutOfRangeException
     * @return float
     */
    public static function truncatedMean(array $values, $percent) {
        if(!$values) throw new ArgumentEmptyException('values');
        if($percent < 0 || $percent >= 0.5) throw new ArgumentOutOfRangeException('percent',"must be at greater than or equal to 0 and less than 0.5");
        sort($values);
        $len = count($values);
        $trim = round($len * $percent);
        $newLen = $len - ($trim * 2);
        if($newLen <= 0) {
            // truncated too much, round the other way
            $trim -= 1;
            $newLen += 2;
        }
        return self::mean(array_slice($values, $trim, $newLen));
    }

    /**
     * Returns the average of some numbers.
     *
     * @param int[]|float[] $values
     * @throws Exceptions\ArgumentEmptyException
     * @return float
     */
    public static function mean(array $values) {
        if(!$values) throw new ArgumentEmptyException('values');
        return array_sum($values)/count($values);
    }

    /**
     * Generates a random number within a specified range.
     *
     * @param int|float $min Minimum value (inclusive)
     * @param int|float $max Maximum value (inclusive)
     * @return float A pseudo-random float value
     */
    public static function randFloat($min = 0, $max = 1) {
        return $min + lcg_value() * ($max - $min);
    }

    /**
     * Generate a random number within the specified range and the given step size.
     *
     * @param int|float $min
     * @param int|float $max
     * @param int|float $step
     * @param bool $max_inclusive True to generate a number in the range [$min,$max], false for [$min,$max)
     * @return float
     */
    public static function rand($min, $max, $step = 1, $max_inclusive=true) {
        // lcg_value() theoretically picks a value in [0,1] inclusive
        // subtract a small number to avoid generating values greater than $max in the event that it rolls a perfect 1
        $offset = $max_inclusive ? 0.99999999999999988897769753748434595763683319091796875 : -0.00000000000000011102230246251565404236316680908203125;
        return floor(lcg_value() * (($max - $min) / $step + $offset)) * $step + $min;
    }

    /**
     * Rounds a float to the nearest step.
     *
     * @param float $val
     * @param int $step
     * @param int $mode
     * @return float
     */
    public static function round($val, $step = 1, $mode = PHP_ROUND_HALF_UP) {
        return round($val / $step, 0, $mode) * $step;
    }
}