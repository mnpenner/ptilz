<?php
namespace Ptilz\Internal;

/**
 * Internal class used to prevent escaping.
 * @internal
 */
class RawSql {
    private string $data;

    function __construct(string $str) {
        $this->data = $str;
    }

    function __toString(): string {
        return $this->data;
    }
}
