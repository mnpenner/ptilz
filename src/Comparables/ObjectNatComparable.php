<?php
namespace Ptilz\Comparables;

class ObjectNatComparable extends ObjectComparable {
    public function Compare($a, $b) {
        return strnatcmp($a->{$this->key}, $b->{$this->key});
    }
}