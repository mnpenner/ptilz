<?php
namespace Ptilz\Comparables;

class ObjectCaseComparable extends ObjectComparable {
    public function Compare($a, $b) {
        return strcasecmp($a->{$this->key}, $b->{$this->key});
    }
}