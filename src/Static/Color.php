<?php

namespace Ptilz;

// https://gist.github.com/mnpenner/6513318

class Color {
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
        $l = ($max + $min)/2.;
        if($max === $min) {
            $h = $s = 0.;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d/(2. - $max - $min) : $d/($max + $min);
            switch($max) {
                case $r:
                    $h = ($g - $b)/$d + ($g < $b ? 6. : 0.);
                    break;
                case $g:
                    $h = ($b - $r)/$d + 2.;
                    break;
                case $b:
                    $h = ($r - $g)/$d + 4.;
                    break;
            }
            $h /= 6.;
        }

        return [$h, $s, $l];
    }

    private static function _hue2rgb($p, $q, $t) {
        if($t < 0) $t += 1;
        if($t > 1) $t -= 1;
        if($t*6 < 1) return $p + ($q - $p)*6*$t;
        if($t*2 < 1) return $q;
        if($t*3 < 2) return $p + ($q - $p)*(2/3 - $t)*6;
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
            $q = $l < 0.5 ? $l*(1 + $s) : $l + $s - $l*$s;
            $p = 2*$l - $q;
            $r = self::_hue2rgb($p, $q, $h + 1/3);
            $g = self::_hue2rgb($p, $q, $h);
            $b = self::_hue2rgb($p, $q, $h - 1/3);
        }

        return [(int)round($r*255), (int)round($g*255), (int)round($b*255)];
    }
}
