<?php

/**
 * Internal class used to prevent escaping.
 * @internal
 */
class RawString {
    private $str;
    function __construct($str) {
        $this->str = $str;
    }
    function __toString() {
        return $this->str;
    }
}