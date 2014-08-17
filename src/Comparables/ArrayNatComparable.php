<?php
namespace Ptilz\Comparables;

class ArrayNatComparable extends ArrayComparable {
    public function Compare($a, $b) {
        return strnatcmp($a[$this->key], $b[$this->key]);
    }
}