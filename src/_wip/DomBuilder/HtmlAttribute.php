<?php

class HtmlAttribute {
    public $key;
    public $value;

    public function __construct($key, $value=null) {
        $this->key = strtolower(trim($key));
        $this->value = $value;
    }

    public function __toString() {
        if($this->value===null) return $this->key;
        if($this->value===false) return '';
        if($this->value===true) $sb = array($this->key,'="',htmlspecialchars($this->key),'"');
        else $sb = array($this->key,'="',htmlspecialchars($this->value),'"');
        return implode('', $sb);
    }
}