<?php
namespace Ptilz\Internal;

/**
 * Internal class used to prevent escaping.
 * @internal
 */
class RawJson {
    private $data;

    function __construct($str) {
        $this->data = $str;
    }

    function __toString() {
        return $this->data;
    }
}