<?php

namespace Ptilz;

// https://gist.github.com/mnpenner/6513318

use Ptilz\Exceptions\NotImplementedException;

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
     * Compresses an int value in [0-$max] to [0-1].
     *
     * @param int $val
     * @param int|float $max
     * @return float|int
     */
    public static function intToFloat($val, $max = 255) {
        return Math::clamp($val / $max, 0, 1);
    }

    /**
     * Clamps an RGB value to 0-255
     *
     * @param int $val
     * @return float|int
     */
    private static function clampRgb($val) {
        return (int)Math::clamp(round($val), 0, 255);
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

    private static $cssColorNames = [
        'aliceblue' => 0xf0f8ff,
        'antiquewhite' => 0xfaebd7,
        'aqua' => 0x00ffff,
        'aquamarine' => 0x7fffd4,
        'azure' => 0xf0ffff,
        'beige' => 0xf5f5dc,
        'bisque' => 0xffe4c4,
        'black' => 0x000000,
        'blanchedalmond' => 0xffebcd,
        'blue' => 0x0000ff,
        'blueviolet' => 0x8a2be2,
        'brown' => 0xa52a2a,
        'burlywood' => 0xdeb887,
        'cadetblue' => 0x5f9ea0,
        'chartreuse' => 0x7fff00,
        'chocolate' => 0xd2691e,
        'coral' => 0xff7f50,
        'cornflower' => 0x6495ed,
        'cornsilk' => 0xfff8dc,
        'crimson' => 0xdc143c,
        'cyan' => 0x00ffff,
        'darkblue' => 0x00008b,
        'darkcyan' => 0x008b8b,
        'darkgoldenrod' => 0xb8860b,
        'darkgray' => 0xa9a9a9,
        'darkgreen' => 0x006400,
        'darkkhaki' => 0xbdb76b,
        'darkmagenta' => 0x8b008b,
        'darkolivegreen' => 0x556b2f,
        'darkorange' => 0xff8c00,
        'darkorchid' => 0x9932cc,
        'darkred' => 0x8b0000,
        'darksalmon' => 0xe9967a,
        'darkseagreen' => 0x8fbc8f,
        'darkslateblue' => 0x483d8b,
        'darkslategray' => 0x2f4f4f,
        'darkturquoise' => 0x00ced1,
        'darkviolet' => 0x9400d3,
        'deeppink' => 0xff1493,
        'deepskyblue' => 0x00bfff,
        'dimgray' => 0x696969,
        'dodgerblue' => 0x1e90ff,
        'firebrick' => 0xb22222,
        'floralwhite' => 0xfffaf0,
        'forestgreen' => 0x228b22,
        'fuchsia' => 0xff00ff,
        'gainsboro' => 0xdcdcdc,
        'ghostwhite' => 0xf8f8ff,
        'gold' => 0xffd700,
        'goldenrod' => 0xdaa520,
        'gray' => 0xbebebe,
        'webgray' => 0x808080,
        'green' => 0x00ff00,
        'webgreen' => 0x008000,
        'greenyellow' => 0xadff2f,
        'honeydew' => 0xf0fff0,
        'hotpink' => 0xff69b4,
        'indianred' => 0xcd5c5c,
        'indigo' => 0x4b0082,
        'ivory' => 0xfffff0,
        'khaki' => 0xf0e68c,
        'lavender' => 0xe6e6fa,
        'lavenderblush' => 0xfff0f5,
        'lawngreen' => 0x7cfc00,
        'lemonchiffon' => 0xfffacd,
        'lightblue' => 0xadd8e6,
        'lightcoral' => 0xf08080,
        'lightcyan' => 0xe0ffff,
        'lightgoldenrod' => 0xfafad2,
        'lightgray' => 0xd3d3d3,
        'lightgreen' => 0x90ee90,
        'lightpink' => 0xffb6c1,
        'lightsalmon' => 0xffa07a,
        'lightseagreen' => 0x20b2aa,
        'lightskyblue' => 0x87cefa,
        'lightslategray' => 0x778899,
        'lightsteelblue' => 0xb0c4de,
        'lightyellow' => 0xffffe0,
        'lime' => 0x00ff00,
        'limegreen' => 0x32cd32,
        'linen' => 0xfaf0e6,
        'magenta' => 0xff00ff,
        'maroon' => 0xb03060,
        'webmaroon' => 0x7f0000,
        'mediumaquamarine' => 0x66cdaa,
        'mediumblue' => 0x0000cd,
        'mediumorchid' => 0xba55d3,
        'mediumpurple' => 0x9370db,
        'mediumseagreen' => 0x3cb371,
        'mediumslateblue' => 0x7b68ee,
        'mediumspringgreen' => 0x00fa9a,
        'mediumturquoise' => 0x48d1cc,
        'mediumvioletred' => 0xc71585,
        'midnightblue' => 0x191970,
        'mintcream' => 0xf5fffa,
        'mistyrose' => 0xffe4e1,
        'moccasin' => 0xffe4b5,
        'navajowhite' => 0xffdead,
        'navyblue' => 0x000080,
        'oldlace' => 0xfdf5e6,
        'olive' => 0x808000,
        'olivedrab' => 0x6b8e23,
        'orange' => 0xffa500,
        'orangered' => 0xff4500,
        'orchid' => 0xda70d6,
        'palegoldenrod' => 0xeee8aa,
        'palegreen' => 0x98fb98,
        'paleturquoise' => 0xafeeee,
        'palevioletred' => 0xdb7093,
        'papayawhip' => 0xffefd5,
        'peachpuff' => 0xffdab9,
        'peru' => 0xcd853f,
        'pink' => 0xffc0cb,
        'plum' => 0xdda0dd,
        'powderblue' => 0xb0e0e6,
        'purple' => 0xa020f0,
        'webpurple' => 0x7f007f,
        'rebeccapurple' => 0x663399,
        'red' => 0xff0000,
        'rosybrown' => 0xbc8f8f,
        'royalblue' => 0x4169e1,
        'saddlebrown' => 0x8b4513,
        'salmon' => 0xfa8072,
        'sandybrown' => 0xf4a460,
        'seagreen' => 0x2e8b57,
        'seashell' => 0xfff5ee,
        'sienna' => 0xa0522d,
        'silver' => 0xc0c0c0,
        'skyblue' => 0x87ceeb,
        'slateblue' => 0x6a5acd,
        'slategray' => 0x708090,
        'snow' => 0xfffafa,
        'springgreen' => 0x00ff7f,
        'steelblue' => 0x4682b4,
        'tan' => 0xd2b48c,
        'teal' => 0x008080,
        'thistle' => 0xd8bfd8,
        'tomato' => 0xff6347,
        'turquoise' => 0x40e0d0,
        'violet' => 0xee82ee,
        'wheat' => 0xf5deb3,
        'white' => 0xffffff,
        'whitesmoke' => 0xf5f5f5,
        'yellow' => 0xffff00,
        'yellowgreen' => 0x9acd32,
    ];


    /**
     * @param string $str CSS color string
     * @return int
     * @throws NotImplementedException
     * @throws \Exception
     */
    public static function cssToInt($str) {
        $str = strtolower(preg_replace('~\s+~', '', $str));
        if(preg_match('~#[0-9a-f]{6}\z~A', $str, $m)) {
            return sscanf($str, '#%06x')[0];
        } elseif(preg_match('~#[0-9a-f]{3}\z~A', $str, $m)) {
            return hexdec($str[1] . $str[1] . $str[2] . $str[2] . $str[3] . $str[3]);  // RRGGBB
        //} elseif(preg_match('~#[0-9a-f]{4}\z~A', $str, $m)) {
        //    return hexdec($str[1] . $str[1] . $str[2] . $str[2] . $str[3] . $str[3] . $str[4] . $str[4]); // AARRGGBB
        //} elseif(preg_match('~#[0-9a-f]{8}\z~A', $str, $m)) {
        //    return sscanf($str, '#%08x')[0];
        } elseif(preg_match('~rgb\((?P<r>\d+),(?P<g>\d+),(?P<b>\d+)\)\z~A', $str, $m)) {
            return (self::clampRgb($m['r']) << 16)
                + (self::clampRgb($m['g']) << 8)
                + self::clampRgb($m['b']);
        } elseif(preg_match('~rgba\((?P<r>\d+),(?P<g>\d+),(?P<b>\d+),(?P<a>\d+(?:\.\d*)?|\.\d+)\)\z~A', $str, $m)) {
            return (self::clampRgb($m['r']) << 16)
                + (self::clampRgb($m['g']) << 8)
                + self::clampRgb($m['b'])
                + (self::floatToInt(1-$m['a']) << 24); // store transparency instead of opacity so that when the upper bits are 0, this means fully opaque which is the default when alpha is omitted
        } elseif(preg_match('~hsl\((?P<h>\d+),(?P<s>\d+)%,(?P<l>\d+)%\)\z~A', $str, $m)) {
            list($r,$g,$b) = self::hslToRgb(self::intToFloat($m['h'],360),self::intToFloat($m['s'],100),self::intToFloat($m['l'],100));
            return ($r << 16) + ($g << 8) + $b;
        } elseif(preg_match('~hsla\((?P<h>\d+),(?P<s>\d+)%,(?P<l>\d+)%,(?P<a>\d+(?:\.\d*)?|\.\d+)\)\z~A', $str, $m)) {
            list($r,$g,$b) = self::hslToRgb(self::intToFloat($m['h'],360),self::intToFloat($m['s'],100),self::intToFloat($m['l'],100));
            return ($r << 16) + ($g << 8) + $b + (self::floatToInt(1-$m['a']) << 24);
        } elseif(preg_match('~[a-z]+\z~A', $str, $m)) {
            if(isset(self::$cssColorNames[$str])) {
                return self::$cssColorNames[$str];
            }
            throw new \Exception("Invalid CSS color name: $str");
        } else {
            throw new \Exception("Could not parse CSS color: $str");
        }
    }

    /**
     * @param int $int Color integer
     * @return string
     */
    public static function intToCss($int) {
        if($int > 0xFFFFFF) {
            $r = ($int >> 16) & 0xff;
            $g = ($int >> 8) & 0xff;
            $b = $int & 0xff;
            $a = round(1-((($int >> 24) & 0xff)/0xff),3);
            return "rgba($r,$g,$b,$a)";
        } else {
            return sprintf('#%06x', $int);
        }
    }
}
