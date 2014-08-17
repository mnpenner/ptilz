<?php
namespace Ptilz;
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
        return self::toInt($val);
    }

    /**
     * Add two arbitrary precision numbers
     *
     * @param int|string $left The left operand, as a string.
     * @param int|string $right The right operand, as a string.
     * @return int|string
     */
    public static function add($left, $right) {
        return self::toInt(bcadd($left, $right));
    }

    /**
     * Multiply two arbitrary precision numbers
     *
     * @param int|string $left The left operand, as a string.
     * @param int|string $right The right operand, as a string.
     * @return int|string
     */
    public static function mul($left, $right) {
        return self::toInt(bcmul($left, $right));
    }

    /**
     * Converts a string integer to a native integer if it's within PHP's valid range of integers
     *
     * @param string $val
     * @return int|string
     */
    public static function toInt($val) {
        return self::between($val, PHP_INT_MIN, PHP_INT_MAX) ? (int)$val : (string)$val;
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

    public static function max($a, $b) {
        return bccomp($a, $b) > 0 ? $a : $b;
    }

    public static function min($a, $b) {
        return bccomp($a, $b) < 0 ? $a : $b;
    }

    public static function randInt($min, $max) {
        $randMax = mt_getrandmax();
        $diff = bcsub($max, $min);
        if(bccomp($diff, $randMax) > 0) {
            trigger_error("Spread is greater than precision of mt_rand(); numbers will be skipped", E_USER_WARNING);
        }
        return self::toInt(bcadd(
            $min,
            bcmul(
                bcdiv(mt_rand(), bcadd($randMax, '1'), 10),
                bcadd($diff, '1')
            )
        ));
    }

    /**
     * Computes the natural logarithm of a number.
     *
     * @param int|string|float $n
     * @param int        $scale This optional parameter is used to set the number of digits after the decimal place in the result.
     * @return string
     */
    public static function ln($n, $scale=10) {
        $iscale = $scale+3;
        $result = '0.0';
        $i = 0;

        do {
            $pow = (1 + (2 * $i++));
            $mul = bcdiv('1', $pow, $iscale);
            $fraction = bcmul($mul, bcpow(bcsub($n, '1', $iscale) / bcadd($n, '1', $iscale), $pow, $iscale), $iscale);
            $lastResult = $result;
            $result = bcadd($fraction, $result, $iscale);
        } while($result !== $lastResult);
//        echo "$i iterations\n";

        return bcmul('2', $result, $scale);
    }

    /**
     * Computes the logarithm of a number.
     *
     * @param int|string|float $n
     * @param int $base
     * @param int $scale This optional parameter is used to set the number of digits after the decimal place in the result.
     * @return string
     */
    public static function log($n, $base = 10, $scale=10) {
        return bcdiv(self::ln($n, $scale), self::ln($base, $scale), $scale);
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
}