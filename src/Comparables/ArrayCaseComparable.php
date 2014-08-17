<?php
namespace Ptilz\Comparables;

class ArrayCaseComparable extends ArrayComparable {
    public function Compare($a, $b) {
        return strcasecmp($a[$this->key], $b[$this->key]);
    }
}