<?php namespace Ptilz;

use Exception;

/**
 * @property-read int $length Current length of the buffer
 * @property-read int $bytesRemaining The number of bytes left to read
 * @property-read string $buffer The buffer (read only)
 */
class BinaryStream {
    private $buf, $bitPos, $bytePos;

    public function __construct($buffer='') {
        $this->buf = $buffer;
        $this->bytePos = 0;
        $this->bitPos = 0;
    }

    public function getPos() {
        return $this->bytePos.':'.$this->bitPos;
    }

    /**
     * Binary to uppercase hex
     *
     * @param string $data
     * @return string
     */
    public static function bin2uhex($data) {
        return strtoupper(bin2hex($data));
    }

    /**
     * Decimal to uppercase hex
     *
     * @param string $dec
     * @return string
     */
    public static function dec2uhex($dec) {
        return '0x'.strtoupper(self::dechex($dec));
    }

    /**
     * Returns a binary representation of the data (1s and 0s!)
     *
     * @param string $bin Binary data
     * @param int    $len Length in bits
     * @return string
     */
    public static function binrep($bin, $len = 32) {
        return implode(' ', str_split(str_pad(decbin(hexdec(bin2hex($bin))), $len, '0', STR_PAD_LEFT), 8));
    }

    public function __toString() {
        return implode("\n", array_map(function ($x) {
            return chunk_split(chunk_split($x, 16, '  '), 2, ' ');
        }, str_split(strtoupper(bin2hex($this->buf)), 32))) . "\n";
    }

    /**
     * Reads a single bit from the stream
     * @return int|null 0 or 1
     */
    private function readBit() {
        if($this->bytePos >= strlen($this->buf)) return null;

        $byte = ord($this->buf[$this->bytePos]);
        $bit = ($byte >> $this->bitPos) & 1;

        if(++$this->bitPos > 7) {
            $this->bitPos = 0;
            ++$this->bytePos;
        }

        return $bit;
    }

    /**
     * @param int $n Number of bits to read
     * @return int|null
     */
    public function readBits($n = 1) {
        $val = 0;
        for($i=0; $i<$n; ++$i) {
            $bit = $this->readBit();
            if($bit === null) return null;
            $val |= $bit << $i;
        }
        return $val;
    }

    /**
     * @param int $n Number of bytes to read
     * @throws Exception
     * @return null|string
     */
    public function readBytes($n = 1) {
        if($this->bitPos !== 0) throw new Exception("Buffer must be byte-aligned before reading bytes");
        if($n === 0) return '';
        $bytes = substr($this->buf, $this->bytePos, $n);
        if($bytes === false) throw new Exception("Could not read $n bytes from buffer at pos $this->bytePos");
        if(strlen($bytes) !== $n) throw new Exception("Could not read requested number of bytes; requested $n, found ".strlen($bytes));
        $this->bytePos += $n;
        return $bytes;
    }

    public function seek($byteOffset, $bitOffset, $whence=SEEK_SET) {
        switch($whence) {
            case SEEK_SET:
                $this->bytePos = $byteOffset;
                $this->bitPos = $bitOffset;
                break;
            case SEEK_CUR:
                $this->bytePos += $byteOffset + floor($bitOffset/8);
                $this->bitPos = ($this->bitPos + $bitOffset) % 8;
                break;
            case SEEK_END;
                $this->bytePos = strlen($this->buf) + $byteOffset;
                $this->bitPos = $bitOffset % 8;
                break;
        }
    }

    /**
     * Returns the next byte in the stream without advancing the cursor
     * @return string
     */
    public function peekByte() {
        return $this->buf[$this->bytePos];
    }


    /**
     * Write a single bit to the buffer
     * @param int $val 0 or 1
     */
    public function writeBit($val) {
        if($this->bitPos === 0) {
            $this->buf .= "\0";
        }
        if($val) {
            $byte = ord($this->buf[strlen($this->buf)-1]);
            $byte |= 1 << $this->bitPos;
            $this->buf[strlen($this->buf)-1] = chr($byte);
        }
        if(++$this->bitPos === 8) {
            $this->bitPos = 0;
            ++$this->bytePos;
        }
    }

    /**
     * Write any number of bits to the buffer.
     * @param int $n Number of bits to write
     * @param int $val Number to pack into these bits
     */
    public function writeBits($n, $val) {
        for($i=0; $i<$n; ++$i) {
            $this->writeBit(((int)$val >> $i) & 1);
        }
    }

    /**
     * Write raw bytes to the buffer. Buffer must be byte-aligned before writing.
     * @param string $bytes
     * @throws \Exception
     */
    public function writeBytes($bytes) {
        if($this->bitPos !== 0) throw new Exception("Buffer must be byte-aligned before writing bytes");
        $this->buf .= $bytes;
    }

    /**
     * Write a serializable object to the buffer.
     * @param ISerializable $obj
     */
    public function write(ISerializable $obj) {
        $obj->writeStream($this);
    }

    public function __get($name) {
        switch($name) {
            case 'length': return strlen($this->buf);
            case 'bytesRemaining': return strlen($this->buf) - $this->bytePos;
            case 'buffer': return $this->buf;
        }
        throw new Exception("Property '$name' is undefined");
    }

    /**
     * @param string $hex Hexidecimal representation of number
     * @return int|string Decimal representation of number
     * @see http://www.php.net/manual/en/ref.bc.php#99130
     */
    public static function hexdec($hex) {
        if(strlen($hex) == 1) {
            return hexdec($hex);
        } else {
            $remain = substr($hex, 0, -1);
            $last = substr($hex, -1);
            return bcadd(bcmul(16, self::hexdec($remain)), hexdec($last));
        }
    }

    /**
     * @param int|string $dec Decimal representation of number
     * @return string Hexidecimal representation of number
     */
    public static function dechex($dec) {
        $last = bcmod($dec, 16);
        $remain = bcdiv(bcsub($dec, $last), 16);

        if($remain == 0) {
            return dechex($last);
        } else {
            return self::dechex($remain).dechex($last);
        }
    }
}