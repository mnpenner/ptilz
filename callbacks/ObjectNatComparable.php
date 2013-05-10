<?php

class ObjectNatComparable extends ObjectComparable {
    function Compare($a, $b) {
        return strnatcmp($a->{$this->key}, $b->{$this->key});
    }
}