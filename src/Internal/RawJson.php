<?php
namespace Ptilz\Internal;
use Ptilz\IJavaScriptSerializable;

/**
 * Internal class used to prevent escaping.
 * @internal
 */
class RawJson implements IJavaScriptSerializable {
    private $data;

    /**
     * @param string $str Valid JSON string.
     */
    function __construct($str) {
        $this->data = (string)$str;
    }

    function __toString() {
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
    public function jsSerialize($options) {
        return $this->data;
    }
}