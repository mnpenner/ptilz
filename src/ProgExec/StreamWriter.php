<?php namespace Ptilz\ProgExec;

class StreamWriter extends Stream {

    /**
     * Write to the stream.
     *
     * @param string $string The string that is to be written.
     * @param int|null $length If the length argument is given, writing will stop after length bytes have been written or the end of string is reached, whichever comes first.
     * @return int Number of bytes written, or FALSE on error.
     */
    public function write($string, $length=null) {
        if($length) {
            return fwrite($this->handle, $string, $length);
        }
        return fwrite($this->handle, $string);
    }
}