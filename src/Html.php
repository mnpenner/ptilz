<?php
namespace Ptilz;

class Html {
    public static function attrs($attrs) {
        if(!$attrs || !is_array($attrs)) return '';
        $attr_pairs = [];
        foreach($attrs as $key => $val) {
            if(!is_string($key) || is_numeric($key)) continue;
            $key = htmlspecialchars(trim($key));
            if($val === true) $attr_pairs[] = $key . '="' . $key . '"';
            elseif($val === false) continue;
            elseif($val === null) $attr_pairs[] = $key;
            elseif(is_string($val)) $attr_pairs[] = $key . '="' . htmlspecialchars($val) . '"';
            elseif(is_int($val) || is_float($val)) $attr_pairs[] = $key . '="' . strval($val) . '"';
            elseif(is_array($val)) {
                if($val === []) continue;
                elseif(Arr::isAssoc($val)) {
                    $style_pairs = [];
                    foreach($val as $k => $v) {
                        $style_pairs[] = "$k:$v";
                    }
                    $attr_pairs[] = $key . '="' . htmlspecialchars(implode(';', $style_pairs)) . '"';
                } else {
                    $attr_pairs[] = $key . '="' . htmlspecialchars(implode(' ', $val)) . '"';
                }
            }
        }
        return $attr_pairs ? ' ' . implode(' ', $attr_pairs) : '';
    }
}