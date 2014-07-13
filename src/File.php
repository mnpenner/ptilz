<?php
namespace Ptilz;

use Exception;
use Ptilz\Exceptions\ArgumentTypeException;
use Ptilz\Exceptions\InvalidOperationException;

class File {
    /**
     * Create a file with a unique random filename in the given directory and return its file handle.
     *
     * @param string $dir Directory to create file in
     * @param string $ext Optional extension
     * @param string $chars Character set to choose filename from
     * @throws Exceptions\InvalidOperationException
     * @return File
     */
    public static function mkunique($dir, $ext = '', $chars='0123456789abcdefghijklmnopqrstuvwxyz_-') {
        for($i = 0; $i < 1000; ++$i) {
            $path = Path::join($dir, Str::random(8, $chars));
            if($ext) $path .= '.' . $ext;
            $fp = @fopen($path, 'x');
            if($fp !== false) return new static($fp, $path);
        }
        throw new InvalidOperationException("Could not create unique file");
    }

    /**
     * Creates a temporary file
     *
     * Creates a temporary file with a unique name in read-write (w+) mode and returns a file handle.
     *
     * The file is automatically removed when closed (for example, by calling fclose(), or when there are no remaining references to the file handle returned by tmpfile()), or when the script ends.
     *
     * For details, consult your system documentation on the tmpfile(3) function, as well as the stdio.h header file.
     *
     * @return static
     */
    public function tmp() {
        return new static(tmpfile());
    }

    /** @var resource File pointer */
    protected $fp;
    /** @var string Absolute file path */
    protected $path;

    /**
     * @param string|resource $filename Filename to open/create or an existing file pointer resource
     * @param string $mode Mode to open file with or filename for opened stream
     * @throws Exceptions\ArgumentTypeException
     * @throws Exceptions\InvalidOperationException
     *
     * @fixme: split this in to two static constructors
     */
    public function __construct($filename = null, $mode = null) {
        if(is_resource($filename)) {
            $this->fp = $filename;
            $this->path = $mode ?: stream_get_meta_data($filename)['uri'];
        } elseif(is_string($filename)) {
            $this->fp = fopen($filename, $mode ?: 'c+');
            if($this->fp === false) throw new InvalidOperationException("Could open file '$filename' in mode '$mode'");
            $this->path = Path::resolve($filename);
        } else {
            throw new ArgumentTypeException("Unsupported type for first argument '".Dbg::getType($filename)."'");
        }
    }

    /**
     * Tests for end-of-file
     *
     * @return bool Returns TRUE if the file pointer is at EOF or an error occurs (including socket timeout); otherwise returns FALSE.
     */
    public function eof() {
        return feof($this->fp);
    }

    public function basename($suffix = null) {
        return basename($this->path, $suffix);
    }

    /**
     * Flushes the output to a file
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function flush() {
        return fflush($this->fp);
    }

    public function __destruct() {
        fclose($this->fp);
    }

    /**
     * Gets a character
     *
     * @return string Returns a string containing a single character read from the file. Returns FALSE on EOF.
     */
    public function getc() {
        return fgetc($this->fp);
    }

    /**
     * Gets line from file pointer and parse for CSV fields
     *
     * @param int $length       Must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). It became optional in PHP 5. Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later) the maximum line length is not limited, which is slightly slower.
     * @param string $delimiter Set the field delimiter (one character only).
     * @param string $enclosure Set the field enclosure character (one character only).
     * @param string $escape    Set the escape character (one character only). Defaults as a backslash.
     * @return array Returns an indexed array containing the fields read.
     */
    public function getcsv($length = 0, $delimiter = ',', $enclosure = '"', $escape = '\\') {
        return fgetcsv($this->fp, $length, $delimiter, $enclosure, $escape);
    }

    /**
     * Gets line from file pointer
     *
     * @param int $length Reading ends when length - 1 bytes have been read, or a newline (which is included in the return value), or an EOF (whichever comes first). If no length is specified, it will keep reading from the stream until it reaches the end of the line.
     * @return string Returns a string of up to length - 1 bytes read from the file pointed to by handle. If there is no more data to read in the file pointer, then FALSE is returned.
     *                    If an error occurs, FALSE is returned.
     */
    public function gets($length = null) {
        return fgets($this->fp, $length);
    }

    /**
     * Gets line from file pointer and strip HTML tags
     *
     * Identical to gets(), except that getss() attempts to strip any NUL bytes, HTML and PHP tags from the text it reads.
     *
     * @param int $length            Length of the data to be retrieved.
     * @param string $allowable_tags Specify tags which should not be stripped.
     * @return string Returns a string of up to length - 1 bytes read from the file pointed to by handle, with all HTML and PHP code stripped.
     *                               If an error occurs, returns FALSE.
     */
    public function getss($length = null, $allowable_tags = null) {
        return fgetss($this->fp, $length, $allowable_tags);
    }

    /**
     * Portable advisory file locking
     *
     * Allows you to perform a simple reader/writer model which can be used on virtually every platform (including most Unix derivatives and even Windows).
     *
     * On versions of PHP before 5.3.2, the lock is released also by fclose() (which is also called automatically when script finished).
     *
     * PHP supports a portable way of locking complete files in an advisory way (which means all accessing programs have to use the same way of locking or it will not work). By default, this function will block until the requested lock is acquired; this may be controlled (on non-Windows platforms) with the LOCK_NB option documented below.
     *
     * @param int $operation  one of the following:
     *
     * LOCK_SH to acquire a shared lock (reader).
     * LOCK_EX to acquire an exclusive lock (writer).
     * LOCK_UN to release a lock (shared or exclusive).
     *
     * It is also possible to add LOCK_NB as a bitmask to one of the above operations if you don't want flock() to block while locking. (not supported on Windows)
     * @param int $wouldblock Set to 1 if the lock would block (EWOULDBLOCK errno condition). (not supported on Windows)
     * @return bool
     */
    public function lock($operation, &$wouldblock = null) {
        return flock($this->fp, $operation, $wouldblock);
    }

    /**
     * Output all remaining data on a file pointer
     *
     *  Reads to EOF on the given file pointer from the current position and writes the results to the output buffer.
     *
     * You may need to call rewind() to reset the file pointer to the beginning of the file if you have already written data to the file.
     *
     * If you just want to dump the contents of a file to the output buffer, without first modifying it or seeking to a particular offset, you may want to use the readfile(), which saves you the fopen() call.
     *
     * @return int If an error occurs, passthru() returns FALSE. Otherwise, passthru() returns the number of characters read from handle and passed through to the output.
     */
    public function passthru() {
        return fpassthru($this->fp);
    }

    /**
     * Format line as CSV and write to file pointer
     *
     * @param array $fields     An array of values.
     * @param string $delimiter The optional delimiter parameter sets the field delimiter (one character only).
     * @param string $enclosure The optional enclosure parameter sets the field enclosure (one character only).
     * @return int Returns the length of the written string or FALSE on failure.
     */
    public function putcsv(array $fields, $delimiter = ',', $enclosure = '"') {
        return fputcsv($this->fp, $fields, $delimiter, $enclosure);
    }

    /**
     * Binary-safe file read
     *
     *  fread() reads up to length bytes from the file pointer referenced by handle. Reading stops as soon as one of the following conditions is met:
     *
     * - length bytes have been read
     * - EOF (end of file) is reached
     * - a packet becomes available or the socket timeout occurs (for network streams)
     * - if the stream is read buffered and it does not represent a plain file, at most one read of up to a number of bytes equal to the chunk size (usually 8192) is made; depending on the previously buffered data, the size of the returned data may be larger than the chunk size.
     *
     * @param int $length Up to length number of bytes read.
     * @return string Returns the read string or FALSE on failure.
     */
    public function read($length) {
        return fread($this->fp, $length);
    }

    /**
     * Get the underlying file handle.
     *
     * @return resource
     */
    public function handle() {
        return $this->fp;
    }

    /**
     * Returns is the absolute path for the open file, if it is available.
     *
     * @return string|null
     */
    public function path() {
        return $this->path;
    }

    /**
     * Seeks on a file pointer
     *
     *  Sets the file position indicator for the file referenced by handle. The new position, measured in bytes from the beginning of the file, is obtained by adding offset to the position specified by whence.
     *
     * In general, it is allowed to seek past the end-of-file; if data is then written, reads in any unwritten region between the end-of-file and the sought position will yield bytes with value 0. However, certain streams may not support this behavior, especially when they have an underlying fixed size storage.
     *
     * @param int $offset The offset.
     *
     * To move to a position before the end-of-file, you need to pass a negative value in offset and set whence to SEEK_END.
     * @param int $whence whence values are:
     *
     * - SEEK_SET - Set position equal to offset bytes.
     * - SEEK_CUR - Set position to current location plus offset.
     * - SEEK_END - Set position to end-of-file plus offset.
     *
     * @return int Upon success, returns 0; otherwise, returns -1.
     */
    public function seek($offset, $whence = SEEK_SET) {
        return fseek($this->fp, $offset, $whence);
    }

    /**
     * Gets information about a file using an open file pointer
     *
     * Gathers the statistics of the file opened by the file pointer handle. This function is similar to the stat() function except that it operates on an open file pointer instead of a filename.
     *
     * @return array Returns an array with the statistics of the file; the format of the array is described in detail on the stat() manual page.
     * @see http://ca2.php.net/manual/en/function.stat.php
     */
    public function stat() {
        return fstat($this->fp);
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int  Returns the position of the file pointer referenced by handle as an integer; i.e., its offset into the file stream.
     *
     * If an error occurs, returns FALSE.
     */
    public function tell() {
        return ftell($this->fp);
    }

    /**
     * Truncates a file to a given length
     *
     * Takes the filepointer, handle, and truncates the file to length, size.
     *
     * @param int $size The size to truncate to.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function truncate($size = 0) {
        return ftruncate($this->fp, $size);
    }

    /**
     * Binary-safe file write
     *
     * @param string $string The string that is to be written.
     * @param int $length    If the length argument is given, writing will stop after length bytes have been written or the end of string is reached, whichever comes first.
     *
     * Note that if the length argument is given, then the magic_quotes_runtime configuration option will be ignored and no slashes will be stripped from string.
     * @return int The number of bytes written, or FALSE on error.
     */
    public function write($string, $length = null) {
        return fwrite($this->fp, $string, $length);
    }
}