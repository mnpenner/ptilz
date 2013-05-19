<?php

class Str {
    public static function startsWith($haystack, $needle, $case_sensitive = true) {
        $substr = substr($haystack, 0, strlen($needle));
        return $case_sensitive ? $substr === $needle : mb_strtolower($substr) === mb_strtolower($needle);
    }

    public static function endsWith($haystack, $needle, $case_sensitive = true) {
        $substr = substr($haystack, -strlen($needle));
        return $case_sensitive ? $substr === $needle : mb_strtolower($substr) === mb_strtolower($needle);
    }

    public static function phpTemplate($__file__, $__vars__) {
        extract($__vars__, EXTR_SKIP);
        ob_start();
        include $__file__;
        return ob_get_clean();
    }

    public static function split($str) {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    private static function strlen($str) {
        return function_exists('mb_strlen') ? mb_strlen($str) : count(self::split($str));
    }

    public static function cEscapeStr($str) {
        return '"'.addcslashes($str,"\0..\37\42\134\177..\377").'"';
    }

    // TODO: add rsplit
}