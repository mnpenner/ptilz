<?php
namespace Ptilz\Comparables;

class ObjectNatCaseComparable extends ObjectComparable {
    function Compare($a, $b) {
        return strnatcasecmp($a->{$this->key}, $b->{$this->key});
    }
}