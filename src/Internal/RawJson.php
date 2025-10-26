<?php
namespace Ptilz\Internal;
use Ptilz\IJavaScriptSerializable;

/**
 * Internal class used to prevent escaping.
 * @internal
 */
class RawJson implements IJavaScriptSerializable {
    private string $data;

    /**
     * @param string $str Valid JSON string.
     */
    function __construct(string $str) {
        $this->data = (string)$str;
    }

    function __toString(): string {
        return $this->data;
    }

    /**
     * Serializes the object into a JSON-compatible string.
     *
     * Options should be respected, if possible.
     *
     * @param int $options The options that were passed to Json::encode
     * @return string
     */
    public function jsSerialize(int $options): string {
        return $this->data;
    }
}
