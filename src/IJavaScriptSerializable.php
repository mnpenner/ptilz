<?php namespace Ptilz;

interface IJavaScriptSerializable {
    /**
     * Serializes the object into a JSON-compatible string.
     *
     * Unlike the built-in JsonSerializable, this returns valid JSON, not something that can then be json_encoded.
     *
     * Options should be respected, if possible.
     *
     * @param int $options The options that were passed to Json::encode
     * @return string
     */
    public function jsSerialize(int $options): string;
}
