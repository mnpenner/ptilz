<?php namespace Ptilz;

class BitStream {
    /** Most significant byte first */
    const BIG_ENDIAN = 1;
    /** Least significant byte first */
    const LITTLE_ENDIAN = 2;

    /** @var string */
    protected $data;
    /** @var int Length in bits */
    protected $length;
    /** @var int Position in bits */
    protected $pos;
    /** @var int */
    protected $byte_order;

    public function __construct($data, $length = null, $byte_order=self::LITTLE_ENDIAN) {
        $this->data = $data;
        $this->length = $length !== null ? $length : strlen($data) * 8;
        $this->pos = 0;
        $this->byte_order = $byte_order;
    }

    /**
     * Read bits.
     *
     * @param int $nbits Up to number of bits to read
     * @param int $total_bits The actual number of bits that were read
     * @return int
     */
    public function read($nbits = 1, &$total_bits=null) {
        if($this->eof()) {
            $total_bits = 0;
            return false;
        }

        $total_bits = min($nbits, $this->length - $this->pos);

        $i = (int)floor($this->pos / 8);
        $offset = $this->pos % 8;

        $bits_left = $total_bits;
        $value = 0;

        while($bits_left > 0) {
            $ord = ord($this->data[$i]);
            $rem_byte = 8 - $offset;
            $read = min($rem_byte, $bits_left);

            if($offset) {
                $ord >>= $offset;
                $offset = 0;
            }
            if($bits_left < $rem_byte) {
                $ord &= (1 << $bits_left) - 1;
            }

            if($this->byte_order === self::BIG_ENDIAN) {
                $value = ($value << $read) | $ord;
            } else {
                $so_far = $total_bits - $bits_left;
                $value = $value | ($ord << $so_far);
            }
            $bits_left -= $read;
            ++$i;
        }

        $this->pos += $total_bits;
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
