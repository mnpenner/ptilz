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

    public function read($bits = 1) {
        $byteOffset = (int)floor($this->pos / 8);
        $bitOffset = $bits % 8;
        $byte = ord($this->data[$byteOffset]);
        $bitLength = min($bits, $this->length - $this->pos);

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

        if($bitLength < 8) {
            $byte &= $masks[$bitLength];
        }


        // shift right, apply mask... repeat


        $this->pos += $bitLength;
        return mt_rand(0,pow(2,$bitLength)-1);
    }

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
