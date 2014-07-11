<?php

class HtmlElement extends HtmlNode {
    /** @var string */
    protected $tag;
    /** @var HtmlAttribute[] */
    protected $attrs;
    /** @var HtmlNode[] */
    protected $children;
    /** @var array Elements whose content model never allows it to have contents under any circumstances. See http://www.w3.org/TR/html-markup/syntax.html#syntax-elements */
    private static $void_elements;

    public static function _staticConstruct() {
        self::$void_elements = array_fill_keys(array('area','base','br','col','command','embed','hr','img','input','input','keygen','link','meta','param','source','track','wbr'), true);
    }

    /**
     * @param string $tag Tag name
     * @param array $attrs Attribute dict
     * @param HtmlNode|HtmlNode[]|string $children Child elements or text
     * @throws Exception
     */
    public function __construct($tag, $attrs=null, $children=null) {
        $this->tag = strtolower(trim($tag));
        $this->attrs = array();
        $this->children = array();

        if($attrs) {
            foreach($attrs as $k=>$v) {
                $k = strtolower($k);
                if($v instanceof HtmlAttribute) {
                    $this->attrs[$k] = $v;
                } elseif($k==='style') {
                    $this->attrs[$k] = new HtmlStyleAttribute($v);
                } elseif($k==='class') {
                    $this->attrs[$k] = new HtmlClassAttribute($v);
                } else {
                    $this->attrs[$k] = new HtmlAttribute($k,$v);
                }
            }
        }
        if($children) {
            if(is_array($children)) {
                $this->children = array_merge($this->children, $children);
            } elseif($children instanceof HtmlNode) {
                $this->children[] = $children;
            } elseif(is_string($children)) {
                $this->children[] = new HtmlTextNode($children);
            } else throw new Exception('Bad type');
        }
    }

    /**
     * @param string|HtmlAttribute $attr Attribute or name
     * @param string|null $value Attribute value
     */
    public function setAttr($attr, $value=null) {
        if($attr instanceof HtmlAttribute) {
            $this->attrs[$attr->key] = $attr;
        } else {
            $this->attrs[strtolower($attr)] = new HtmlAttribute($attr, $value);
        }
    }

    /**
     * @param string|HtmlAttribute $attr Attribute or name
     */
    public function removeAttr($attr) {
        if($attr instanceof HtmlAttribute) {
            unset($this->attrs[$attr->key]);
        } else {
            unset($this->attrs[strtolower($attr)]);
        }
    }

    /**
     * @param string $key Attribute name
     * @return string Attribute value
     */
    public function getAttr($key) {
        return isset($this->attrs[$key]) ? $this->attrs[$key]->value : null;
    }

    /**
     * @param HtmlNode|string $child
     * @throws Exception
     * @return $this
     */
    public function append($child) {
        if(isset(self::$void_elements[$this->tag])) throw new Exception("Cannot append children to void element '$this->tag'");
        $this->children[] = $child instanceof HtmlNode ? $child : new HtmlTextNode($child);
        return $this;
    }

    public function appendTo(HtmlElement $parent) {
        $parent->append($this);
        return $this;
    }

    private static function ae(&$arr) {
        $args = array_slice(func_get_args(),1);
        foreach($args as $a) {
            $arr = array_merge($arr, (array)$a);
        }
    }

    public function __toString() {
        $sb = array('<',$this->tag);
        if($this->attrs) {
            $sb[] = ' ';
            $sb[] = implode(' ',array_filter($this->attrs, function($a) { return (string)$a!==''; }));
        }
        if(isset(self::$void_elements[$this->tag])) {
            $sb[] = ' />';
        } else {
            $sb[] = '>';
            foreach($this->children as $child) {
                $sb[] = (string)$child;
            }
            self::ae($sb, '</',$this->tag,'>');
        }
        return implode('',$sb);
    }
}

HtmlElement::_staticConstruct();