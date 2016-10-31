<?php namespace Ptilz;

abstract class Uuid {

    /**
     * @param string $buf
     * @param int $nbits
     * @param bool $keep_left_over
     * @return array
     * @see http://codegolf.stackexchange.com/questions/75935/split-a-byte-array-into-a-bit-array
     */
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
     * Example output: ""ka41ybpbqs5hppt7ky42sxr4b""
     *
     * @return string
     * @see http://www.crockford.com/wrmg/base32.html
     */
    public static function uuid() {
        return self::_uuid(16);
    }

    /**
     * Generates a UUID of arbitrary length. Made private because we don't really want people choosing their own lengths.
     *
     * @param int $bytes
     * @return string
     */
    private static function _uuid($bytes) {
        $bytes = Bin::secureRandomBytes($bytes);
        $bits = self::regroupBits($bytes, 5, false);

        return implode('', array_map(function ($x) {
            return Str::CROCKFORD32[$x];
        }, $bits));
    }

    /**
     * Number of milcroseconds since unix epoch. Where a "milcrosecond" is 1/10,000th of a second or 100 microseconds.
     * @return int
     */
    private static function milcrotime() {
        return (int)round(microtime(true)*10000);
    }

    /**
     * Ordered UUID.
     * 
     * Example output: "0de4ey5pm5qt1ceh1cvz63r85byr8w"
     * 
     * @return string
     * @throws \Exception
     */
    public static function ouid() {
        // http://www.wolframalpha.com/input/?i=unix+epoch+%2B+2**50%2F10000+seconds
        // https://www.percona.com/blog/2014/12/19/store-uuid-optimized-way/
        // on my Core i7 6700K, I can only generate up to 9 of these in the same milcrosecond,
        // so that alone already provides a great deal of uniqueness
        
        $prefix = Math::decToAnyBase(self::milcrotime(), null, Str::CROCKFORD32);
        if(strlen($prefix) > 10) {
            throw new \Exception("Are you still using this function in the year 5537? It seems to have overflowed.");
        } else {
            $prefix = str_pad($prefix, 10, '0', STR_PAD_LEFT);
        }
        
        // 13 bytes rounds to 100 bits of entropy, giving us 150 bits total, for a nice round 30 chars
        // 100 bits gives us about a 0.00003944% chance of collision with 1 trillion records
        // combined with the "milcrotime" prefix, the odds should be effectively 0
        // in other words, you'd have to generate about 500,000,000,000,000 OUIDs within 1/10,000th of a second
        // for a 10% chance of a collision. Even if your system clocks are off, the odds are very low.
        
        return $prefix . self::_uuid(13);
    }

    /**
     * @param string $ouid An OUID as returned by \Ptilz\Uuid::ouid. i.e., a 10+ char string encoded with Crockford-32.
     * @return \DateTime
     */
    public static function extractDate($ouid) {
        $prefix = substr($ouid, 0, 10);
        $milcrotime = Math::anyBaseToDec($prefix, null, Str::CROCKFORD32);
        return date_create_from_format('U.u', number_format($milcrotime/10000,6,'.',''));
    }
}