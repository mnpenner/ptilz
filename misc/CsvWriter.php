<?php

class CsvWriter {
    private $fp;
    private $delim;
    private $enc;

    public function __construct($filename, $delimiter=',', $enclosure='"') {
        $this->fp = fopen($filename,'w');
        $this->delim = $delimiter;
        $this->enc = $enclosure;
    }

    public function write($fields) {
        fputcsv($this->fp, $fields, $this->delim, $this->enc);
    }

    public function __destruct() {
        fclose($this->fp);
    }
}