<?php namespace Ptilz;

abstract class Uuid {


    private static function regroupBits($buf, $nbits, $keep_left_over) {
        $mask = (1 << $nbits) - 1;
        $nworkbits = 0;
        $workbuf = 0;
        $buflen = strlen($buf);
        $resultlen = strlen($buf)*8/$nbits;
        $resultlen = $keep_left_over ? ceil($resultlen) : floor($resultlen);
        $result = array_fill(0, $resultlen, 0);
        $i = 0;
        $j = 0;

        while(true) {
            while($nworkbits < $nbits) {
                if($i >= $buflen) break 2;
                $workbuf <<= 8;
                $workbuf += ord($buf[$i]);
                $nworkbits += 8;
                ++$i;
            }
            $offset = $nworkbits - $nbits;
            $result[$j++] = ($workbuf >> $offset) & $mask;
            $nworkbits -= $nbits;
        }
        if($nworkbits && $keep_left_over) {
            $workbuf <<= $nbits - $nworkbits;
            $result[$j] = $workbuf & $mask;
        }

        return $result;
    }

    /**
     * Generate a 125-bit (25 alphanumeric ASCII chars) universally unique identifier. Encode using Crockford's base-32 encoding algorithm
     * to make it:
     *
     * - Remove some look-alike characters
     * - 11 bytes less than a v4 GUID in hex form (with dashes)
     * - Only 3 bytes more more than binary form encoded as base 64
     * - Case-insensitive
     * - URL safe
     * - Cannot contain the F word
     *
     * This function should *never* return the same string twice unless it's broken.
     *
     * @return string
     * @see http://www.crockford.com/wrmg/base32.html
     */
    public static function uuid() {
        $bytes = Bin::secureRandomBytes(16);
        $bits = self::regroupBits($bytes, 5, false);

        return implode('', array_map(function ($x) {
            return '0123456789abcdefghjkmnpqrstvwxyz'[$x];
        }, $bits));
    }

    public static function ouid() {

    }
}