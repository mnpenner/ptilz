<?php namespace Ptilz\ProgExec;

class StreamWriter extends Stream {

    /**
     * Write to the stream.
     *
     * @param string $string The string that is to be written.
     * @param int|null $length If the length argument is given, writing will stop after length bytes have been written or the end of string is reached, whichever comes first.
     * @return int The number of bytes written, or FALSE on error.
     */
    public function write($string, $length=null) {
        if($length) {
            return fwrite($this->handle, $string, $length);
        }
        return fwrite($this->handle, $string);
    }

    /**
     * Write a string terminated by a line-break.
     *
     * @param string $string The string that is to be written.
     * @param string $eol End-of-line character(s)
     * @return int The number of bytes written, or FALSE on error.
     */
    public function writeLine($string, $eol=PHP_EOL) {
        return fwrite($this->handle, $string.$eol);
    }
}