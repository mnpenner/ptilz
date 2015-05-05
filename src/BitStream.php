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
        if($this->eof()) return null;

        $value = 0;

        $bitLength = min($bits, $this->length - $this->pos);


        $byteOffset = (int)floor($this->pos / 8);
        $bitOffset = abs(8 - ($this->pos + $bitLength)) % 8;
        $byte = ord($this->data[$byteOffset]) >> $bitOffset;


        $valueMult = ($bitLength - 1) % 8;

        $i = 0;
        while(true) {
            $bit = $byte & 1;
            $value |= $bit << $valueMult;
            --$valueMult;
            if(++$i >= $bitLength) {
                break;
            }
            if(++$bitOffset >= 8) {
                $bitOffset = 0;
                $byte = ord($this->data[++$byteOffset]);
                $valueMult += 16;
            } else {
                $byte >>= 1;
            }
        }

        $this->pos += $bitLength;

        return $value;
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
