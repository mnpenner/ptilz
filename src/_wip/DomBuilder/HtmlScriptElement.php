<?php

use Ptilz\Str;

class HtmlScriptElement extends HtmlElement {
    public function __construct($code, $vars = array()) {
        parent::__construct('script', array('type' => 'text/javascript'), new HtmlTextNode($vars ? Str::replace_assoc(array_map('Json::encode', $vars), $code) : $code, false));
    }
}