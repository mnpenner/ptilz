<?php

class ArrayNatCaseComparable extends ArrayComparable {
    function Compare($a, $b) {
        return strnatcasecmp($a[$this->key], $b[$this->key]);
    }
}