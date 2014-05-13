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

    /**
     * Splits a string into an array of characters. Works on multi-byte strings.
     *
     * @param $str
     * @return array
     */
    public static function splitChars($str) {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Returns the length of a string. Works on multi-byte strings.
     * @param $str
     * @return int
     */
    private static function strlen($str) {
        return function_exists('mb_strlen') ? mb_strlen($str) : preg_match_all("/./us",$str);
    }

    public static function cEscapeStr($str) {
        return '"'.addcslashes($str,"\0..\37\42\134\177..\377").'"';
    }

    /**
     * Strip HTML and PHP tags from a string.
     *
     * @param string $html
     * @param string|array $allowable_tags Tags which should be stripped. Should be in the form of '<b><i><u>' or array('b','i','u')
     * @param bool $allow_comments Allow HTML comments
     * @return mixed|string HTML with tags stripped out
     */
    public static function strip_tags($html, $allowable_tags, $allow_comments=false) {
        if(is_array($allowable_tags)) $allowable_tags = '<' . implode('><', $allowable_tags) . '>';
        $parts = $allow_comments ? preg_split('`(<!--.*?-->)`s', $html, -1, PREG_SPLIT_DELIM_CAPTURE) : array($html);
        foreach($parts as $i => $p) {
            if(($i & 1) === 0) {
                $parts[$i] = strip_tags($p, $allowable_tags);
            }
        }
        return implode('', $parts);
    }

    /**
     * Split a string into an array using a delimiter, working from right to left, up to the specified number of elements.
     *
     * @param string $str   The input string.
     * @param string $delim The boundary string.
     * @param int $limit    Maximum of limit elements with the last element containing the rest of string.
     * @param mixed $pad    If $limit and $pad are provided, the result will be padded up to the limit with this value. Useful when used with list(...) = rsplit(...) to avoid warnings.
     * @return array
     */
    public static function rsplit($str,$delim,$limit=PHP_INT_MAX,$pad=null) {
        $parts = array();
        for($i=$limit; $i>1; --$i) {
            $pos = strrpos($str,$delim);
            if($pos === false) break;
            array_unshift($parts,substr($str,$pos+1));
            $str = substr($str,0,$pos);
        }
        array_unshift($parts,$str);
        if(func_get_args() >= 4 && $limit !== PHP_INT_MAX && count($parts)<$limit) {
            $parts = array_pad($parts, $limit, $pad);
        }
        return $parts;
    }

    /**
     * @static
     * @param array $dict Search => Replace pairs
     * @param string $input Input
     * @return string
     */
    public static function replace_assoc($dict,$input) {
        return str_replace(array_keys($dict),array_values($dict),$input);
    }

    public static function random($len, $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
        $str = '';
        $randMax = strlen($chars)-1;

        while($len--) {
            $str .= $chars[mt_rand(0,$randMax)];
        }

        return $str;
    }

    public static function isEmpty($str){
        return $str === null || trim($str) === '';
    }
}