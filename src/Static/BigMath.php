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
     * @param int|string|float $n An arbitrarily large number
     * @return string
     */
    public static function ln($n) {
        return self::log10($n) / 0.43429448190325182765112891891660508229439700580367;
    }

    /**
     * @param int|string|float $n An arbitrarily large number
     * @return float
     * @throws \Exception
     * @license Matthew Slyman @aaabit.com//CC-BY-SA-3.0
     * @see http://stackoverflow.com/a/33441820/65387
     */
    private static function log10($n) {
        $m = [];
        preg_match('/^(-)?0*([1-9][0-9]*)?\.?(0*)([1-9][0-9]*)?$/', $n, $m);
        if(!isset($m[1])) {
            throw new \Exception('Argument: not decimal number string!');
        }
        $sgn = $m[1];
        if($sgn === '-') {
            throw new \Exception('Cannot compute: log(<⁺0)!');
        }
        $abs = $m[2];
        $pos = strlen($abs);
        $fre = $m[3];
        $neg = strlen($fre);
        if(isset($m[4])) {
            $frc = $m[4];
        } else {
            $frc = '';
        }
        if($pos === 0) {
            $dec_frac = '.' . substr($frc, 0, 15);
            $pos = -1 * $neg;
        } else {
            $dec_frac = '.' . substr($abs . $fre . $frc, 0, 15);
        }
        return log10((float)$dec_frac) + (float)$pos;
    }

    /**
     * Computes the logarithm of a number.
     *
     * @param int|string|float $n An arbitrarily large number
     * @param int $base
     * @return string
     */
    public static function log($n, $base = 10) {
        if($base === 10) return self::log10($n);
        if($base === M_E) return self::log10($n) / 0.43429448190325182765112891891660508229439700580367;
        if($base === 2) return self::log10($n) / 0.30102999566398119521373889472449302676818988146211;
        return self::log10($n) / self::log10($base);
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