<?php
namespace Ptilz;

use Exception;
use Iterator;
use IteratorAggregate;
use Ptilz\Exceptions\InvalidOperationException;

class CsvReader implements IteratorAggregate {
    /** @var resource */
    private $fp = null;
    /** @var array|bool */
    private $headers = false;
    /** @var int */
    private $start_pos = 0;
    /** @var string */
    private $delimiter;
    /** @var string */
    private $enclosure;
    /** @var string */
    private $escape;
    /** @var int  */
    private $max_length;

    /**
     * @param string $filename    Filename of CSV
     * @param bool|int $header    True to use the first row as array keys, integer to skip this many lines before reading the header, false to use numeric indices
     * @param int $skip_lines     Skip this many lines (after the header) before reading data
     * @param string $delimiter   Field delimiter (one character only)
     * @param string $enclosure   Field enclosure character (one character only)
     * @param string $escape      Escape character (one character only)
     * @param int $max_length     Must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). Omitting this parameter (or setting it to 0 in PHP 5.0.4 and later) the maximum line length is not limited, which is slightly slower.
     *
     * @throws Exception
     */
    public function __construct($filename, $header = false, $skip_lines = 0, $delimiter = ',', $enclosure = '"', $escape = '\\', $max_length = 0) {
        $this->fp = fopen($filename, 'r');
        if($this->fp === false) throw new InvalidOperationException("Could not open '$filename' for reading'");
        if($header !== false) {
            if(is_int($header)) {
                while($header--) {
                    fgets($this->fp);
                }
            }
            $this->headers = fgetcsv($this->fp);
        }
        while($skip_lines--) {
            fgets($this->fp);
        }
        $this->start_pos = ftell($this->fp);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->max_length = $max_length;
    }

    public function readline() {
        $line = fgetcsv($this->fp, $this->max_length, $this->delimiter, $this->enclosure, $this->escape);
        if($line !== false && $this->headers) {
            $line = self::zipdict($this->headers, $line);
        }
        return $line;
    }

    private static function zipdict($keys, $values) {
        $len = min(count($keys),count($values));
        $out = array();
        for($i=0; $i<$len; ++$i) {
            $out[$keys[$i]?:$i] = $values[$i];
        }
        return $out;
    }

    public function __destruct() {
        fclose($this->fp);
    }

    public function rewind() {
        fseek($this->fp, $this->start_pos, SEEK_SET);
    }

    public function getIterator() {
        return new CsvIterator($this);
    }
}

class CsvIterator implements Iterator {
    private $csv;
    private $line;
    private $line_nbr;

    public function __construct(CsvReader $csv) {
        $this->csv = $csv;
    }

    public function current() {
        return $this->line;
    }

    public function next() {
        $this->line = $this->csv->readline();
        ++$this->line_nbr;
    }

    public function key() {
        return $this->line_nbr;
    }

    public function valid() {
        return $this->line !== false;
    }

    public function rewind() {
        $this->csv->rewind();
        $this->line_nbr = -1;
        $this->next();
    }
}