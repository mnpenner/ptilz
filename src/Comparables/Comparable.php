<?php
namespace Ptilz\Comparables;

abstract class Comparable {
    abstract public function Compare($a, $b);

    public function __invoke($a, $b) {
        return $this->Compare($a, $b);
    }
}