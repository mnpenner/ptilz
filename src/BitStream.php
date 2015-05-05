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

        $i = (int)floor($this->pos / 8);
        $offset = $this->pos % 8;

        $left = $read;
        $value = 0;

        while($left > 0) {
            $ord = ord($this->data[$i]);
            $rem = 8 - $offset;
            $r = min($rem, $left);

            if($offset) {
                $ord >>= $offset;
                $offset = 0;
            }
            if($left < $rem) {
                $ord &= (1 << $left) - 1;
            }
            $value = ($value << $r) + $ord;
            $left -= $r;
            ++$i;
        }

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

    public function rewind() {
        $this->pos = 0;
    }

    public function seek($offset, $whence = SEEK_SET) {
        switch($whence) {
            case SEEK_SET:
                $this->pos = $offset;
                break;
            case SEEK_CUR:
                $this->pos += $offset;
                break;
            case SEEK_END:
                $this->pos = $this->length + $offset;
                break;
            default:
                throw new \DomainException("Bad whence: $whence");
        }
    }


    function __toString() {
        // TODO: XXXX out the unused bits
        return implode(PHP_EOL, array_map(function($s) {
            return implode(' ',str_split(str_pad(decbin(hexdec(bin2hex($s))),strlen($s)*8,'0',STR_PAD_LEFT),8));
        }, str_split($this->data, PHP_INT_SIZE)));
    }
}
