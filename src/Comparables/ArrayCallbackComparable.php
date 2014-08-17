<?php
namespace Ptilz\Comparables;

class ArrayCallbackComparable extends Comparable {
    protected $key;
    protected $func;

    function __construct($key, $func) {
        $this->key = $key;
        $this->func = $func;
    }

    public function Compare($a, $b) {
        return call_user_func($this->func, $a[$this->key], $b[$this->key]);
    }
}