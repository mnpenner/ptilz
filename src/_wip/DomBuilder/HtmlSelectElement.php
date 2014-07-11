<?php

class HtmlSelectElement extends HtmlElement {
    public function __construct($attrs=null, $children=null) {
        parent::__construct('select', $attrs, $children);
    }

    public function setValue($value) {
        foreach($this->children as $opt) {
            /** @var $opt HtmlElement */
            if($opt->getAttr('value') == $value) {
                $opt->setAttr('selected',true);
            } else {
                $opt->removeAttr('selected');
            }
        }
        return $this;
    }
}