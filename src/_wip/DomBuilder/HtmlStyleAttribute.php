<?php

class HtmlStyleAttribute extends HtmlAttribute {
    public function __construct($style_dict) {
        $this->key = 'style';
        if($style_dict) {
            if(is_array($style_dict)) {
                $properties = array();
                foreach($style_dict as $k=>$v) {
                    if(!$v) continue;
                    if(is_int($k)) {
                        $properties[] = $v;
                    } else {
                        $properties[] = "$k:$v";
                    }
                }
                $this->value = implode(';',$properties);
            } else {
                $this->value = $style_dict;
            }
        } else {
            $this->value = false;
        }
    }
}