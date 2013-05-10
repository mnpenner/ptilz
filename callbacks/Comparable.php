<?php

abstract class Comparable {
    abstract function Compare($a, $b);

    function __invoke($a, $b) {
        return $this->Compare($a, $b);
    }
}