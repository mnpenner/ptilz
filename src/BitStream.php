<?php namespace Ptilz;

class BitStream {
    /** @var string */
    protected $data;
    /** @var int Length in bits */
    protected $length;
    /** @var int Position in bits */
    protected $pos;

    // TODO: Add endianness/bit order parameter
    public function __construct($data, $length = null) {
        $this->data = $data;
        $this->length = func_num_args() >= 2 ? $length : strlen($data) * 8;
        $this->pos = 0;
    }

    /**
     * Read bits.
     *
     * @param int $length Up to number of bits to read
     * @param int $read The actual number of bits that were read
     * @return int
     */
    public function read($length = 1, &$read=null) {
        if($this->eof()) {
            $read = 0;
            return false;
        }

        $read = min($length, $this->length - $this->pos);


        $byteOffset = (int)floor($this->pos / 8);
        $firstByteBitOffset = $this->pos % 8;
        $byteLength = (int)ceil(($read + $firstByteBitOffset)/8);

        $data = substr($this->data, $byteOffset, $byteLength);


        if($firstByteBitOffset) {
            $data[0] = chr(ord($data[0]) >> $firstByteBitOffset);
        }

        $value = hexdec(bin2hex($data)); // reinterpret bytes as int




        //dump($firstByteBitOffset);

        //$value >>= $firstByteBitOffset;
        $value &= (1 << $read) - 1;


        //$value <<= (8 - $firstByteBitOffset) % 8;





        //if($byteLength > 1) {
        //    $lastByteBitOffset = ($firstByteBitOffset + $read) % 8;
        //    if($lastByteBitOffset !== 0) {
        //        $data = substr($data,0,-1).chr(ord(substr($data,-1)) << $lastByteBitOffset);
        //    }
        //} else {
        //    $lastByteBitOffset = 0;
        //}
        //
        //$value = hexdec(bin2hex($data));
        //
        //echo "\n$firstByteBitOffset $lastByteBitOffset";
        //dump($value);
        //
        //
        //if($firstByteBitOffset) {
        //    if($byteLength === 1) {
        //        $value &= (1 << ($firstByteBitOffset+1)) -1;
        //    } else {
        //        $value <<= $firstByteBitOffset;
        //        $value >>= ($firstByteBitOffset + $lastByteBitOffset);
        //    }
        //}

        $this->pos += $read;
        return $value;
    }

    /**
     * End of bit stream has been reached.
     *
     * @return bool
     */
    public function eof() {
        return $this->pos >= $this->length;
    }



    function __toString() {
        // TODO: XXXX out the unused bits
        return implode(PHP_EOL, array_map(function($s) {
            return implode(' ',str_split(str_pad(decbin(hexdec(bin2hex($s))),strlen($s)*8,'0',STR_PAD_LEFT),8));
        }, str_split($this->data, PHP_INT_SIZE)));
    }
}
