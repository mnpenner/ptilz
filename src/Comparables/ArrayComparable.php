<?php
namespace Ptilz\Comparables;

class ArrayComparable extends Comparable {
    protected $key;

    function __construct($key) {
        $this->key = $key;
    }

    function Compare($a, $b) {
        if($a[$this->key] == $b[$this->key]) return 0;
        return $a[$this->key] < $b[$this->key] ? -1 : 1;
    }
}