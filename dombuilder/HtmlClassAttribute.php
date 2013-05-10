<?php

class HtmlClassAttribute extends HtmlAttribute {
    public function __construct($classes) {
        $this->key = 'class';
        if($classes) {
            $this->value = is_array($classes) ? implode(' ',array_filter($classes)) : $classes;
        } else {
            $this->value = false;
        }
    }
}