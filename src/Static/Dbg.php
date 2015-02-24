<?php
namespace Ptilz;

use Ptilz\Exceptions\NotImplementedException;

abstract class Dbg {
    public static function dumpHtml($var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    public static function dumpCli($var, $max) {
        self::dumpCliR($var, $max, 0);
    }

    private static function dumpCliR($var, $max, $depth) {
        if(is_null($var)) {
            echo 'null';
        } elseif(is_resource($var)) {
            echo strval($var);
        } elseif(is_string($var)) {
            // todo: add binary string detection and display as hex
            // if it's a long piece of binary (> 16 bytes?) then split w/ spaces and line breaks
            echo '"' . addcslashes($var, "\0..\37\42\177..\377") . '"';
        } elseif(is_bool($var)) {
            echo $var ? 'true' : 'false';
        } elseif(is_int($var) || is_float($var)) {
            // todo: detect unix timestamp? (+/- 5 years)
            echo $var;
        } elseif(is_array($var)) {
            echo 'array(';
            if(!$var) {
                echo ')';
            } else {
                if($max === null || $max < 0 || $depth < $max) {
                    foreach($var as $k => $v) {
                        echo PHP_EOL . str_repeat(' ', ($depth + 1) * 4) . $k . ' => ';
                        self::dumpCliR($v, $max, $depth + 1);
                    }
                    echo PHP_EOL . str_repeat(' ', $depth * 4) . ')';
                } else {
                    echo '...)';
                }
            }
        } elseif(is_object($var) || is_a($var, '__PHP_Incomplete_Class')) {
            if(method_exists($var, '__toString')) {
                echo '{' . get_class($var) . '} ' . (string)$var;
            } else {
                echo get_class($var) . '{';
                if($max === null || $max < 0 || $depth < $max) {
                    foreach($var as $k => $v) {
                        echo PHP_EOL . str_repeat(' ', ($depth + 1) * 4) . $k . ' = ';
                        self::dumpCliR($v, $max, $depth + 1);
                    }
                    echo PHP_EOL . str_repeat(' ', $depth * 4) . '}';
                } else {
                    echo '...}';
                }
            }
        } else {
            echo gettype($var);
        }
        if($depth == 0) echo PHP_EOL;
    }

    public static function dump() {
        if(Env::isCli()) {
            foreach(func_get_args() as $arg) {
                self::dumpCli($arg, 3);
            }
        } else {
            foreach(func_get_args() as $arg) {
                self::dumpHtml($arg);
            }
        }
    }

    /**
     * @param $var
     * @return string
     * @deprecated Moved to V::
     */
    public static function getType($var) {
        return V::getType($var);
    }
}