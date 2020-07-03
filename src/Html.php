<?php
namespace Ptilz;

class Html {
    public static function attrs($attrs) {
        if(!$attrs || !is_array($attrs)) return '';
        $attr_pairs = [];
        foreach($attrs as $key => $val) {
            if(Str::isInt($key)) {
                $attr_pairs[] = self::escAttrName($val);
            } else {
                $attr = self::escAttrName($key);
                if($val === true) $attr_pairs[] = $attr;
                elseif($val === false || $val === null) continue;
                elseif(is_string($val)) $attr_pairs[] = $attr . '="' . self::escAttrVal($val) . '"';
                elseif(is_int($val) || is_float($val)) $attr_pairs[] = $attr . '="' . strval($val) . '"';
                elseif(is_array($val)) {
                    $val = Arr::filter($val);
                    if($val === []) continue;
                    elseif(Arr::isAssoc($val)) {
                        $attr_pairs[] = $attr . '="' . self::escAttrVal(self::buildStyle($val)) . '"';
                    } else {
                        $attr_pairs[] = $attr . '="' . self::escAttrVal(self::buildClass($val)) . '"';
                    }
                }
            }
        }
        return $attr_pairs ? ' ' . implode(' ', $attr_pairs) : '';
    }

    private static function escAttrName($attrVal) {
        return htmlspecialchars($attrVal, ENT_QUOTES|ENT_HTML5|ENT_DISALLOWED|ENT_SUBSTITUTE);
    }

    private static function escAttrVal($attrVal) {
        return htmlspecialchars($attrVal, ENT_COMPAT|ENT_HTML5|ENT_DISALLOWED|ENT_SUBSTITUTE);
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

    /**
     * Merges arrays of HTML attributes together.
     *
     * Attribute names will be converted to lowercase.
     * If the attribute values are arrays, they will be merged together.
     * "class" attributes will be appended.
     * "style" attributes will be merged.
     * Otherwise, attributes with the same name will overwrite those to the left.
     *
     * @return array
     */
    public static function mergeAttrs() {
        $attr_arrays = func_get_args();
        if(!$attr_arrays) return [];
        $out = [];
        $class = [];
        $style = [];
        foreach($attr_arrays as $arr) {
            foreach($arr as $name=>$val) {
                if(Str::startsWith($name,'data-',true)) {
                    // don't modify case of data attributes
                    $name = 'data-'.substr($name,5);
                } else {
                    $name = mb_strtolower($name);
                }
                switch($name) {
                    case 'class':
                        if(is_array($val)) {
                            $class = array_merge($class, array_values($val));
                        } else {
                            $class = array_merge($class, self::parseClass($val));
                        }
                        $out[$name] = '';
                        break;
                    case 'style':
                        if(is_array($val)) {
                            $style = array_merge($style, $val);
                        } else {
                            $style = array_merge($style, self::parseStyle($val));
                        }
                        $out[$name] = '';
                        break;
                    default:
                        $out[$name] = $val;
                        break;
                }
            }
        }
        if($class) $out['class'] = self::buildClass($class);
        if($style) $out['style'] = self::buildStyle($style);
        return $out;
    }

    /**
     * Primitive "style" attribute parser. Does not handle escaped semi-colons or colons.
     *
     * @param string $style_str
     * @return array
     */
    private static function parseStyle($style_str) {
        $props = preg_split('~\s*;\s*~',trim($style_str));
        $out = [];
        foreach($props as $prop) {
            $p = explode(':',$prop,2);
            if(count($p) === 2) {
                $out[$p[0]] = $p[1];
            } else {
                $out[] = $prop; // bad style property
            }
        }
        return $out;
    }

    private static function buildStyle($style_arr) {
        $style_pairs = [];
        foreach($style_arr as $k => $v) {
            if(strlen($v)) {
                if(is_int($k)) {
                    $style_pairs[] = $v;
                } else {
                    $style_pairs[] = "$k:$v";
                }
            }
        }
        return implode(';', $style_pairs);
    }

    private static function parseClass($class_str) {
        return preg_split('~\s+~',trim($class_str));
    }

    private static function buildClass($class_arr) {
        return implode(' ',array_unique(array_filter(array_map('trim',$class_arr),'strlen')));
    }

    /**
     * Prepend "data-" to each of the array keys and JSON-encode values as necessary.
     *
     * @param array $data
     * @return array
     * @throws Exceptions\InvalidOperationException
     */
    public static function dataAttrs(array $data) {
        $attrs = [];
        foreach($data as $k=>$v) {
            $attrs["data-$k"] = Str::unquote(Json::encode($v, JSON_ESCAPE_SCRIPTS));
        }
        return $attrs;
    }
}