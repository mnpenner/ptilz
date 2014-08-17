<?php
namespace Ptilz\Comparables;

class ObjectNatCaseComparable extends ObjectComparable {
    public function Compare($a, $b) {
        return strnatcasecmp($a->{$this->key}, $b->{$this->key});
    }
}