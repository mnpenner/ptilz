<?php namespace Ptilz;

// TODO: make this implement IteratorAggregate ... or rather Iterator (http://php.net/manual/en/class.iterator.php) since this class *is* the bit stream iterator, and the string itself is the aggregate
// also.. implement a writeable version
// implement PSR7 stream

class BitStream {
    /** @var string */
    protected $data;
    /** @var int Length in bits */
    protected $length;
    /** @var int Position in bits */
    protected $pos;

    public function __construct($data, $length = null) {
        $this->data = $data;
        $this->length = $length !== null ? $length : strlen($data) * 8;
        $this->pos = 0;
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

        //echo "read $nbits bits starting at $i:$offset from data of length $this->length\n";


        while($bits_left > 0) {
            $ord = ord($this->data[$i]);
            $rem_byte = 8 - $offset;
            $offset = 0;
            $read = min($rem_byte, $bits_left);

            if($bits_left < $rem_byte) {
                $ord >>= ($rem_byte - $bits_left);
            }

            if($read < 8) {
                $ord &= (1 << $read) - 1;
            }

            $value = ($value << $read) | $ord;

            $bits_left -= $read;
            ++$i;
        }

        if($total_bits < $nbits) {
            $value <<= ($nbits - $total_bits); // needed to conform with b64; this is like padding with 0s
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
        $so_far = 0;
        $str = implode(PHP_EOL, array_map(function ($s) use (&$so_far) {
            $nbits = strlen($s) * 8;
            if($so_far >= $this->length) {
                $bit_str = str_repeat('X', $nbits);
            } else {
                $bit_str = str_pad(decbin(hexdec(bin2hex($s))), $nbits, '0', STR_PAD_LEFT);
                $repl = $nbits - ($this->length - $so_far);
                if($repl > 0) {
                    $bit_str = substr($bit_str, 0, -$repl) . str_repeat('X', $repl);
                }
            }
            $so_far += $nbits;
            return implode(' ', str_split($bit_str, 8));
        }, str_split($this->data, PHP_INT_SIZE)));
        return $str;
    }
}
