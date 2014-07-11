<?php
namespace Ptilz\Comparables;

class ArrayNatComparable extends ArrayComparable {
    function Compare($a, $b) {
        return strnatcmp($a[$this->key], $b[$this->key]);
    }
}