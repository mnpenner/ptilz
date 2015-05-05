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
     * @param int $read_bits The actual number of bits that were read
     * @return int
     */
    public function read($length = 1, &$read_bits=null) {
        if($this->eof()) {
            $read_bits = 0;
            return false;
        }

        $byteOffset = (int)floor($this->pos / 8);
        $bitOffset = $length % 8;
        $byte = ord($this->data[$byteOffset]);
        $read_bits = min($length, $this->length - $this->pos);

        static $masks = [
            1 => 0b00000001,
            2 => 0b00000011,
            3 => 0b00000111,
            4 => 0b00001111,
            5 => 0b00011111,
            6 => 0b00111111,
            7 => 0b01111111,
        ];

        if($bitOffset > 0) {
            $byte >>= $bitOffset;
        }

        if($read_bits < 8) {
            $byte &= $masks[$read_bits];
        }


        // shift right, apply mask... repeat


        $this->pos += $read_bits;
        return mt_rand(0,pow(2,$read_bits)-1);
    }

    /**
     * End of bit stream has been reached.
     *
     * @return bool
     */
    public function eof() {
        return $this->pos >= $this->length;
    }

    /**
     * Returns a binary representation of the data (1s and 0s!)
     *
     * @param string $bin Binary data
     * @param int $len Length in bits
     * @return string
     */
    private static function binrep($bin, $len = null) {
        if($len === null) $len = strlen($bin) * 8;
        return implode(' ', str_split(str_pad(decbin(hexdec(bin2hex($bin))), $len, '0', STR_PAD_LEFT), 8));
    }

    function __toString() {
        return implode(PHP_EOL, array_map(function($s) {
            return implode(' ',str_split(str_pad(decbin(hexdec(bin2hex($s))),strlen($s)*8,'0',STR_PAD_LEFT),8));
        }, str_split($this->data, 4)));
    }
}
