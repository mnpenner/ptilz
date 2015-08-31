<?php

namespace Ptilz;

// https://gist.github.com/mnpenner/6513318

class Color {
    #region RGB-HSL
    /**
     * Converts an RGB color value to HSL. Conversion formula
     * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
     *
     * @param int $r The red color value [0-255]
     * @param int $g The green color value [0-255]
     * @param int $b The blue color value [0-255]
     * @return array The HSL representation in [H, S, L] format, where each value is in [0-1]
     * @link http://stackoverflow.com/a/9493060/65387
     */
    public static function rgbToHsl($r, $g, $b) {
        $r /= 255.;
        $g /= 255.;
        $b /= 255.;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = 0.;
        $l = ($max + $min) / 2.;
        if($max === $min) {
            $h = $s = 0.;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2. - $max - $min) : $d / ($max + $min);
            switch($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6. : 0.);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2.;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4.;
                    break;
            }
            $h /= 6.;
        }

        return [$h, $s, $l];
    }

    private static function _hue2rgb($p, $q, $t) {
        if($t < 0) $t += 1;
        if($t > 1) $t -= 1;
        if($t * 6 < 1) return $p + ($q - $p) * 6 * $t;
        if($t * 2 < 1) return $q;
        if($t * 3 < 2) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }

    /**
     * Converts an HSL color value to RGB. Conversion formula
     * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
     *
     * @param float $h The hue [0-1]
     * @param float $s The saturation [0-1]
     * @param float $l The lightness [0-1]
     * @return array The RGB representation in [R,G,B] format, where each value is in [0-255]
     */
    public static function hslToRgb($h, $s, $l) {
        if($s < 0.0005) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = self::_hue2rgb($p, $q, $h + 1 / 3);
            $g = self::_hue2rgb($p, $q, $h);
            $b = self::_hue2rgb($p, $q, $h - 1 / 3);
        }

        return [(int)round($r * 255), (int)round($g * 255), (int)round($b * 255)];
    }
    #endregion

    #region HUSL
    private static function conv_husl_lch($tuple) {
        list($H, $S, $L) = $tuple;
        if($L > 99.9999999 || $L < 0.00000001) {
            $C = 0;
        } else {
            $max = self::maxChromaForLH($L, $H);
            $C = $max / 100 * $S;
        }
        return [$L, $C, $H];
    }

    private static function maxChromaForLH($L, $H) {
        $hrad = $H / 360 * M_PI * 2;
        $lengths = [];
        $ref = self::getBounds($L);
        for($j = 0, $len1 = count($ref); $j < $len1; ++$j) {
            $line = $ref[$j];
            $L = self::lengthOfRayUntilIntersect($hrad, $line);
            if($L !== null) {
                $lengths[] = $L;
            }
        }
        return min($lengths);
    }

    private static function lengthOfRayUntilIntersect($theta, $line) {
        list($m1, $b1) = $line;
        $len = $b1 / (sin($theta) - $m1 * cos($theta));
        if($len < 0) return null;
        return $len;
    }

    private static $epsilon = 0.0088564516790356308;
    private static $kappa = 903.2962962962963;

    private static $m = [
        'R' => [3.2409699419045214, -1.5373831775700935, -0.49861076029300328],
        'G' => [-0.96924363628087983, 1.8759675015077207, 0.041555057407175613],
        'B' => [0.055630079696993609, -0.20397695888897657, 1.0569715142428786]
    ];

    //private static $refX = 0.95045592705167173;
    private static $refY = 1.0;
    //private static $refZ = 1.0890577507598784;
    private static $refU = 0.19783000664283681;
    private static $refV = 0.468319994938791;

    private static $m_inv = [
        'X' => [0.41239079926595948, 0.35758433938387796, 0.18048078840183429],
        'Y' => [0.21263900587151036, 0.71516867876775593, 0.072192315360733715],
        'Z' => [0.019330818715591851, 0.11919477979462599, 0.95053215224966058]
    ];

    private static function getBounds($L) {
        $sub1 = (($L + 16) ** 3) / 1560896;
        $sub2 = $sub1 > self::$epsilon ? $sub1 : $L / self::$kappa;
        $ret = [];
        $ref = ['R', 'G', 'B'];
        for($j = 0, $len1 = count($ref); $j < $len1; ++$j) {
            $channel = $ref[$j];
            list($m1, $m2, $m3) = self::$m[$channel];
            $ref2 = [0, 1];
            for($k = 0, $len2 = count($ref2); $k < $len2; ++$k) {
                $t = $ref2[$k];
                $top1 = (284517 * $m1 - 94839 * $m3) * $sub2;
                $top2 = (838422 * $m3 + 769860 * $m2 + 731718 * $m1) * $L * $sub2 - 769860 * $t * $L;
                $bottom = (632260 * $m3 - 126452 * $m2) * $sub2 + 126452 * $t;
                $ret[] = [$top1 / $bottom, $top2 / $bottom];
            }
        }
        return $ret;
    }

    /**
     * Convert from HUSL colorspace to RGB.
     *
     * @param float $h Hue [0-360]
     * @param float $s Saturation [0-100]
     * @param float $l Lightness [0-100]
     * @return array [R,G,B] in [0-1]
     * @see http://www.husl-colors.org/
     */
    public static function huslToRgb($h, $s, $l) {
        return self::conv_lch_rgb(self::conv_husl_lch([$h, $s, $l]));
    }

    public static function huslToRgb255($h, $s, $l) {
        return array_map(function ($x) {
            return self::floatToInt($x);
        }, self::huslToRgb($h, $s, $l));
    }

    /**
     * Stretches a float value in [0-1] to [0-$max]
     *
     * @param float $val
     * @param int|float $max
     * @return int
     */
    public static function floatToInt($val, $max = 255) {
        return (int)Math::clamp(round($val * $max), 0, $max);
    }

    /**
     * Stretches an int value in [0-$max] to [0-1].
     *
     * @param int $val
     * @param int|float $max
     * @return float|int
     */
    public static function intToFloat($val, $max = 255) {
        return Math::clamp($val / $max, 0, 1);
    }

    private static function conv_lch_rgb($tuple) {
        return self::conv_xyz_rgb(self::conv_luv_xyz(self::conv_lch_luv($tuple)));
    }

    private static function conv_lch_luv($tuple) {
        list($L, $C, $H) = $tuple;
        $Hrad = $H / 360 * 2 * M_PI;
        $U = cos($Hrad) * $C;
        $V = sin($Hrad) * $C;
        return [$L, $U, $V];
    }

    private static function conv_luv_xyz($tuple) {
        list($l, $u, $v) = $tuple;
        if($l < 0.00000001) {
            return [0, 0, 0];
        }
        $varU = $u / (13 * $l) + self::$refU;
        $varV = $v / (13 * $l) + self::$refV;
        $y = self::L_to_Y($l);
        $x = 0 - (9 * $y * $varU) / (($varU - 4) * $varV - $varU * $varV);
        $z = (9 * $y - (15 * $varV * $y) - ($varV * $x)) / (3 * $varV);
        return [$x, $y, $z];
    }

    private static function L_to_Y($L) {
        if($L <= 8) {
            return self::$refY * $L / self::$kappa;
        } else {
            return self::$refY * (($L + 16) / 116) ** 3;
        }
    }

    private static function conv_xyz_rgb($tuple) {
        $R = self::fromLinear(self::dotProduct(self::$m['R'], $tuple));
        $G = self::fromLinear(self::dotProduct(self::$m['G'], $tuple));
        $B = self::fromLinear(self::dotProduct(self::$m['B'], $tuple));
        return [$R, $G, $B];
    }


    private static function fromLinear($c) {
        if($c <= 0.0031308) {
            return 12.92 * $c;
        } else {
            return 1.055 * ($c ** (1 / 2.4)) - 0.055;
        }
    }

    private static function dotProduct($a, $b) {
        $ret = 0;
        for($i = $j = 0, $ref = count($a) - 1; 0 <= $ref ? $j <= $ref : $j >= $ref; $i = 0 <= $ref ? ++$j : --$j) {
            $ret += $a[$i] * $b[$i];
        }
        return $ret;
    }

    /**
     * Convert RGB to HUSL.
     *
     * @param float $r Red [0-1]
     * @param float $g Green [0-1]
     * @param float $b Blue [0-1]
     * @return float[] [H[0-360], S[0-100], L[0-100]]
     */
    public static function rgbToHusl($r, $g, $b) {
        return self::conv_rgb_husl([$r, $g, $b]);
    }

    public static function rgb255ToHusl($r, $g, $b) {
        return array_map(function ($x) {
            return self::intToFloat($x);
        }, self::rgbToHusl($r, $g, $b));
    }

    /**
     * @param int $val 0xRRGGBB
     * @return int[] [R[0-255], G[0-255], B[0-255]]
     */
    public static function intToRgb($val) {
        return [
            ($val >> 16) & 0xFF,
            ($val >> 8) & 0xFF,
            $val & 0xFF
        ];
    }

    /**
     * @param int $r Red [0-255]
     * @param int $g Green [0-255]
     * @param int $b Blue [0-255]
     * @return int 0xRRGGBB
     */
    public static function rgbToInt($r, $g, $b) {
        return $r + ($g << 8) + ($b << 16);
    }

    /**
     * Convert HUSLp to RGB.
     *
     * @param float $H Hue [0-360]
     * @param float $S Saturation [0-100]
     * @param float $L Lightness [0-100]
     * @return float[] [R[0-1], G[0-1], B[0-1]]
     */
    public static function huslpToRgb($H, $S, $L) {
        return self::conv_xyz_rgb(self::conv_luv_xyz(self::conv_lch_luv(self::conv_huslp_lch([$H, $S, $L]))));
    }

    private static function conv_rgb_xyz($tuple) {
        list($R, $G, $B) = $tuple;
        $rgbl = [self::toLinear($R), self::toLinear($G), self::toLinear($B)];
        $X = self::dotProduct(self::$m_inv['X'], $rgbl);
        $Y = self::dotProduct(self::$m_inv['Y'], $rgbl);
        $Z = self::dotProduct(self::$m_inv['Z'], $rgbl);
        return [$X, $Y, $Z];
    }

    private static function conv_xyz_luv($tuple) {
        list($X, $Y, $Z) = $tuple;

        $L = self::Y_to_L($Y);

        if($L < 0.00000001) {
            return [0, 0, 0];
        }

        $W = $X + (15 * $Y) + (3 * $Z);
        $varU = (4 * $X) / $W;
        $varV = (9 * $Y) / $W;

        $U = 13 * $L * ($varU - self::$refU);
        $V = 13 * $L * ($varV - self::$refV);
        return [$L, $U, $V];
    }

    private static function Y_to_L($Y) {
        if($Y <= self::$epsilon) {
            return ($Y / self::$refY) * self::$kappa;
        } else {
            return 116 * (($Y / self::$refY) ** (1 / 3)) - 16;
        }
    }

    private static function conv_luv_lch($tuple) {
        list($L, $U, $V) = $tuple;
        $C = sqrt($U ** 2 + $V ** 2);
        if($C < 0.00000001) {
            $H = 0;
        } else {
            $Hrad = atan2($V, $U);
            $H = $Hrad * 360 / 2 / M_PI;
            if($H < 0) {
                $H = 360 + $H;
            }
        }
        return [$L, $C, $H];
    }

    /**
     * Convert RGB to HUSL.
     *
     * @param float $r Red [0-1]
     * @param float $g Green [0-1]
     * @param float $b Blue [0-1]
     * @return float[] [H[0-360], S[0-100], L[0-100]]
     */
    public static function rgbToHuslp($r, $g, $b) {
        return self::conv_lch_huslp(self::conv_luv_lch(self::conv_xyz_luv(self::conv_rgb_xyz([$r, $g, $b]))));
    }

    private static function conv_lch_huslp($tuple) {
        list($L, $C, $H) = $tuple;
        if($L > 99.9999999 || $L < 0.00000001) {
            $S = 0;
        } else {
            $max = self::maxSafeChromaForL($L);
            $S = $C / $max * 100;
        }
        return [$H, $S, $L];
    }

    private static function maxSafeChromaForL($L) {
        $lengths = [];
        $ref = self::getBounds($L);
        for($j = 0, $len1 = count($ref); $j < $len1; ++$j) {
            list($m1, $b1) = $ref[$j];
            $x = self::intersectLineLine([$m1, $b1], [-1 / $m1, 0]);
            $lengths[] = self::distanceFromPole([$x, $b1 + $x * $m1]);
        }
        return min($lengths);
    }

    private static function intersectLineLine($line1, $line2) {
        return ($line1[1] - $line2[1]) / ($line2[0] - $line1[0]);
    }

    private static function distanceFromPole($point) {
        return sqrt($point[0] ** 2 + $point[1] ** 2);
    }

    private static function toLinear($c) {
        $a = 0.055;
        if($c > 0.04045) {
            return (($c + $a) / (1 + $a)) ** 2.4;
        } else {
            return $c / 12.92;
        }
    }

    private static function conv_rgb_husl($tuple) {
        return self::conv_lch_husl(self::conv_rgb_lch($tuple));
    }

    private static function conv_rgb_lch($tuple) {
        return self::conv_luv_lch(self::conv_xyz_luv(self::conv_rgb_xyz($tuple)));
    }

    private static function conv_lch_husl($tuple) {
        list($L, $C, $H) = $tuple;
        if($L > 99.9999999 || $L < 0.00000001) {
            $S = 0;
        } else {
            $max = self::maxChromaForLH($L, $H);
            $S = $C / $max * 100;
        }
        return [$H, $S, $L];
    }

    private static function conv_huslp_lch($tuple) {
        list($H, $S, $L) = $tuple;
        if($L > 99.9999999 || $L < 0.00000001) {
            $C = 0;
        } else {
            $max = self::maxSafeChromaForL($L);
            $C = $max / 100 * $S;
        }
        return [$L, $C, $H];
    }

    /**
     * @param int $r Red [0-255]
     * @param int $g Green [0-255]
     * @param int $b Blue [0-255]
     * @return string "#RRGGBB"
     */
    public static function rgbToHex($r, $g, $b) {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * @param int $val 0xRRGGBB
     * @return string "#RRGGBB"
     */
    public static function intToHex($val) {
        return sprintf('#%06x', $val);
    }

    #endregion
}
