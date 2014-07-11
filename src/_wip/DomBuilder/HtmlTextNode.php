<?php

class HtmlTextNode extends HtmlNode {
    protected $text;

    public function __construct($text, $escape=false) {
        $this->text = $escape ? htmlspecialchars($text) : $text;
    }

    public function __toString() {
        return $this->text;
    }
}