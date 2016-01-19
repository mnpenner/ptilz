<?php namespace Ptilz\ProgExec;

class StreamReader extends Stream {

    /**
     * Read some bytes.
     *
     * Reading stops as soon as one of the following conditions is met:
     * - length bytes have been read
     * - EOF (end of file) is reached
     * - a packet becomes available or the socket timeout occurs (for network streams)
     * - if the stream is read buffered and it does not represent a plain file, at most one read of up to a number of bytes equal to the chunk size (usually 8192) is made; depending on the previously buffered data, the size of the returned data may be larger than the chunk size.
     *
     * @param int $length The maximum bytes to read. Defaults to -1 (read all the remaining buffer).
     * @return string Returns the read string or FALSE on failure.
     */
    public function read($length) {
        return fread($this->handle, $length);
    }

    /**
     * Read until the stream is closed. Will *not* return if the stream is paused (e.g. waiting for input).
     *
     * @param int $maxlength The maximum bytes to read. If not set, read all the remaining buffer.
     * @return string Returns a string or FALSE on failure.
     */
    public function readAll($maxlength = null) {
        return stream_get_contents($this->handle, $maxlength === null ? -1 : $maxlength);
    }

    /**
     * Reads a line from the stream.
     *
     * @param int $maxlength Reading ends when length - 1 bytes have been read, or a newline (which is not included in the return value), or an EOF (whichever comes first). If no length is specified, it will keep reading from the stream until it reaches the end of the line.
     * @return string Returns a string of up to length - 1 bytes read from the file pointed to by handle. If there is no more data to read in the file pointer, then FALSE is returned.
     *  If an error occurs, FALSE is returned.
     */
    public function readLine($maxlength=null) {
        // stream_get_line will *not* return if the stream is paused
        // fgets does not allow an ending delimiter (and it returns the end delimiter)

        if($maxlength !== null) {
            $line = fgets($this->handle, $maxlength);
        } else {
            $line = fgets($this->handle);
        }

        return rtrim($line, "\r\n");
    }
}