<?php

class ArrayCaseComparable extends ArrayComparable {
    function Compare($a, $b) {
        return strcasecmp($a[$this->key], $b[$this->key]);
    }
}