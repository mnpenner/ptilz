<?php namespace Ptilz;

class BitStream {
    /** @var string */
    protected $data;
    /** @var int Length in bits */
    protected $length;
    /** @var int Position in bits */
    protected $pos;

    public function __construct($data, $length=null) {
        $this->data = $data;
        $this->length = func_num_args() >= 2 ? $length : strlen($data)*8;
        $this->pos = 0;

        echo PHP_EOL.BinaryStream::binrep($data,$this->length).PHP_EOL;
    }

    public function read($bits = 1) {
        if($this->eof()) return null;
        echo "------------\n";

        $value = 0;

        $bitLength = min($bits, $this->length - $this->pos);
        //$byteOffset = (int)floor($this->pos / 8);
        //$byteLength = ceil($bitLength/8);
        //$data = substr($this->data, $byteOffset, $byteLength);
        //$binary = str_pad(decbin(hexdec(bin2hex($data))), $byteLength*8, '0', STR_PAD_LEFT);
        //$bitOffset = $this->pos % 8;
        //echo "$binary $bitOffset $bitLength\n";
        //$result = bindec(strrev(substr($binary,$bitOffset, $bitLength)));
        //$this->pos += $bitLength;
        //return $result;


        $byteOffset = (int)floor($this->pos / 8);
        $bitOffset = abs(8 - ($this->pos + $bitLength)) % 8;
        $byte = ord($this->data[$byteOffset]) >> $bitOffset;

        //dump($bitLength,$byteOffset,$bitOffset,$byte);

        $valueMult = ($bitLength-1)%8;

        $i = 0;
        while(true) {
            $bit = $byte & 1;
            //echo "i: $i, bitLength: $bitLength, bit: $bit, bitOffset: $bitOffset, byteOffset: $byteOffset, valueMult: $valueMult\n";
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
}