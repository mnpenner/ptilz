<?php

/**
 * Internal class used to prevent escaping.
 * @internal
 */
class BinaryString {
    private $data;
    function __construct($bin) {
        $this->data = ($bin === null || $bin === '') ? "''" : '0x'.bin2hex($bin);
    }
    function __toString() {
        return $this->data;
    }
}