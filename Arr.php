<?php

class Arr {
    public static function get($arr, $key, $default = null) {
        return array_key_exists($key, $arr) ? $arr[$key] : $default;
    }
    
    // TODO: add split and rsplit
}