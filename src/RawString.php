<?php
namespace Ptilz;

/**
 * Internal class used to prevent escaping.
 * @internal
 */
class RawString {
    private $data;

    function __construct($str) {
        $this->data = $str;
    }

    function __toString() {
        return $this->data;
    }
}