<?php namespace Ptilz\ProgExec;

class StreamReader extends Stream {

    /**
     * Read some bytes.
     *
     * @param int $length The maximum bytes to read. Defaults to -1 (read all the remaining buffer).
     * @return string Returns the read string or FALSE on failure.
     */
    public function read($length = -1) {
        if($length < 0) {
            return stream_get_contents($this->handle);
        } else {
            return fread($this->handle, $length);
        }
    }
}