<?php
namespace Ptilz;

class Html {
    public static function attrs($attrs) {
        if(!$attrs || !is_array($attrs)) return '';
        $attr_pairs = [];
        foreach($attrs as $key => $val) {
            if(Str::isInt($key)) {
                $attr_pairs[] = htmlspecialchars($val);
            } else {
                $attr = htmlspecialchars(trim($key));
                if($val === true) $attr_pairs[] = $attr;
                elseif($val === false || $val === null) continue;
                elseif(is_string($val)) $attr_pairs[] = $attr . '="' . htmlspecialchars($val) . '"';
                elseif(is_int($val) || is_float($val)) $attr_pairs[] = $attr . '="' . strval($val) . '"';
                elseif(is_array($val)) {
                    $val = Arr::filter($val);
                    if($val === []) continue;
                    elseif(Arr::isAssoc($val)) {
                        $style_pairs = [];
                        foreach($val as $k => $v) {
                            $style_pairs[] = "$k:$v";
                        }
                        $attr_pairs[] = $attr . '="' . htmlspecialchars(implode(';', $style_pairs)) . '"';
                    } else {
                        $attr_pairs[] = $attr . '="' . htmlspecialchars(implode(' ', $val)) . '"';
                    }
                }
            }
        }
        return $attr_pairs ? ' ' . implode(' ', $attr_pairs) : '';
    }

    /**
     * Strip HTML and PHP tags from a string.
     *
     * @param string $html HTML
     * @param string|array $allowable_tags Tags which should be stripped. Should be in the form of '<b><i><u>' or array('b','i','u')
     * @param bool $allow_comments Allow HTML comments
     * @return mixed|string HTML with tags stripped out
     */
    public static function stripTags($html, $allowable_tags = null, $allow_comments = false) {
        if(is_array($allowable_tags)) $allowable_tags = Arr::wrap($allowable_tags, '<', '>', '');
        $parts = $allow_comments ? preg_split('`(<!--.*?-->)`s', $html, -1, PREG_SPLIT_DELIM_CAPTURE) : [$html];
        foreach($parts as $i => $p) {
            if(($i & 1) === 0) {
                $parts[$i] = strip_tags($p, $allowable_tags);
            }
        }
        return implode('', $parts);
    }
}