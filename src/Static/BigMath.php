<?php
namespace Ptilz;

abstract class BigMath {

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
    public static function ln($n, $scale = 10) {
        $iscale = $scale + 3;
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
    public static function log($n, $base = 10, $scale = 10) {
        return bcdiv(self::ln($n, $scale), self::ln($base, $scale), $scale);
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

    public static function max($a, $b) {
        return bccomp($a, $b) > 0 ? $a : $b;
    }

    public static function min($a, $b) {
        return bccomp($a, $b) < 0 ? $a : $b;
    }
}