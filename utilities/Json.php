<?php

class Json {
    public static function encode($var) {
        if(is_scalar($var)) {
            return json_encode($var);
        }
        if(is_array($var)) {
            if(Arr::isDict($var)) {
                $bits = array();
                foreach($var as $k => $v) {
                    $bits[] = json_encode($k) . ':' . self::encode($v);
                }
                return '{' . implode(',', $bits) . '}';
            } else {
                return '[' . implode(',',array_map(array('self', __FUNCTION__), $var)) . ']';
            }
        }
        if(is_object($var)) {
            if($var instanceof _JsLiteral) {
                return $var->str;
            }
            if($var instanceof JsonSerializable) {
                return $var->jsonSerialize();
            }
            return json_encode($var);
        }
        throw new JsonException('Could not json encode variable of type '.Dbg::getType($var));
    }

    public static function literal($str) {
        return new _JsLiteral($str);
    }
}

if(!interface_exists('JsonSerializable')) {
    interface JsonSerializable {
        public function jsonSerialize();
    }
}

class _JsLiteral {
    public $str;
    function __construct($str) {
        $this->str = $str;
    }
}