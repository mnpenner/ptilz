<?php
namespace Ptilz;
use Ptilz\Exceptions\NotImplementedException;

/**
 * Command-line methods
 */
abstract class Cli { // fixme: rename to Console:: ?

    public static function write($format) {
        $args = func_get_args();
        $args[0] = self::colorize($args[0]);
        echo call_user_func_array(['\Ptilz\Str', 'format'], $args);
    }

    public static function writeLine($format) {
        $args = func_get_args();
        $args[0] = self::colorize($args[0]);
        echo call_user_func_array(['\Ptilz\Str', 'format'], $args) . PHP_EOL;
    }

    /**
     * Converts HTML-like tags into ANSI/VT100 control sequences and decodes HTML entities.
     *
     * @param string $str
     * @return mixed
     */
    public static function colorize($str) {
        // see http://misc.flogisoft.com/bash/tip_colors_and_formatting
        $replaceMatch = function($m) {
            if($m[0] === '/') {
                switch(substr($m,1)) {
                    case 'fg': return '39';
                    case 'bg': return '49';
                    case 'all': return '0';
                    case 'b':
                    case 'strong':
                    case 'bright':
                    case 'bold': return '2';  // '21' should work, but it doesn't...?
                    case 'd':
                    case 'dim': return '22';
                    case 'u':
                    case 'underscore':
                    case 'underline': return '24';
                    case 'blink': return '25';
                    case 'hidden':
                    case 'conceal': return '28';
                    default: return null;
                }
            } else {
                $attrs = explode(';',$m);
                $codes = [];
                foreach($attrs as $attr) {
                    if(strpos($attr,':') !== false) {
                        list($ground, $colorName) = explode(':',$attr,2);
                        if(preg_match('~\d+\z~A',$colorName)) {
                            switch($ground) {
                                case 'fg': $codes[] = '38'; break;
                                case 'bg': $codes[] = '48'; break;
                                default: return null;
                            }
                            $codes[] = '5';
                            $codes[] = $colorName;
                        } else {
                            $colorNumber = 0;
                            switch($ground) {
                                case 'fg': $colorNumber += 30; break;
                                case 'bg': $colorNumber += 40; break;
                                default: return null;
                            }
                            switch($colorName) {
                                case 'black': $colorNumber += 0; break;
                                case 'red': $colorNumber += 1; break;
                                case 'green': $colorNumber += 2; break;
                                case 'yellow': $colorNumber += 3; break;
                                case 'blue': $colorNumber += 4; break;
                                case 'magenta': $colorNumber += 5; break;
                                case 'cyan': $colorNumber += 6; break;
                                case 'light-grey':
                                case 'light-gray': $colorNumber += 7; break;
                                case 'dark-grey':
                                case 'default': $colorNumber += 9; break;
                                case 'dark-gray': $colorNumber += 60; break;
                                case 'light-red': $colorNumber += 61; break;
                                case 'light-green': $colorNumber += 62; break;
                                case 'light-yellow': $colorNumber += 63; break;
                                case 'light-blue': $colorNumber += 64; break;
                                case 'light-magenta': $colorNumber += 65; break;
                                case 'light-cyan': $colorNumber += 66; break;
                                case 'white': $colorNumber += 67; break;
                                default: return null;
                            }
                            $codes[] = $colorNumber;
                        }
                    } else {
                        switch($attr) {
                            case 'reset':
                            case 'clear':
                            case 'default': $codes[] = '0'; break;
                            case 'b':
                            case 'bold':
                            case 'strong':
                            case 'bright': $codes[] = '1'; break;
                            case 'd':
                            case 'dim': $codes[] = '2'; break;
                            case 'u':
                            case 'underline':
                            case 'underscore': $codes[] = '4'; break;
                            case 'blink': $codes[] = '5'; break;
                            case 'inverse':
                            case 'reverse': $codes[] = '7'; break;
                            case 'hidden':
                            case 'conceal': $codes[] = '8'; break;
                            default: return null;
                        }
                    }
                }
                return implode(';',$codes);
            }
        };

        return htmlspecialchars_decode(preg_replace_callback('~<(?<tag>[^>]+)>~', function ($m) use ($replaceMatch) {
            $code = $replaceMatch($m[1]);
            return $code === null ? $m[0] : "\033[{$code}m";
        }, $str), ENT_QUOTES|ENT_HTML5);
    }

    /**
     * Prints an array of strings in columns in order to fit nicely within a terminal window.
     *
     * @param string[] $items
     */
    public static function printColumns($items) {
        $maxWidth = self::width(80);

        $colPadding = '  ';
        $paddingWidth = strlen(strip_tags($colPadding));
        $columns = [$items];
        $colWidths = [max(array_map('strlen', $items))];
        for($nColumns = 2; $nColumns <= count($items); ++$nColumns) {
            $testChunks = array_chunk($items, ceil(count($items) / $nColumns));
            $totalWidth = ($nColumns-1)*$paddingWidth;
            $testWidths = [];
            foreach($testChunks as $x=>$c) {
                $width = max(array_map('strlen', $c));
                $testWidths[] = $width;
                $totalWidth += $width;
                if($totalWidth > $maxWidth) break 2;
            }
            $columns = $testChunks;
            $colWidths = $testWidths;
        }

        for($y=0; $y<count($columns[0]); ++$y) {
            for($x = 0; $x < count($columns); ++$x) {
                if($y >= count($columns[$x])) break;
                echo str_pad($columns[$x][$y], $colWidths[$x], ' ', STR_PAD_RIGHT);
                if($x < count($colWidths) - 1) echo $colPadding;
            }
            echo PHP_EOL;
        }
    }

    /**
     * Gets the width of the terminal window
     *
     * @param mixed $default What to return if the native commands fail
     * @return int|null
     */
    public static function width($default) {
        if(Env::isWindows()) {
            if(preg_match('~\bCON:\n(?:.*\n){2}.*?(?<cols>\d+)$~m', `mode`, $matches)) {
                return (int)$matches['cols'];
            }
        } else {
            $cols = @`tput cols`;
            if($cols !== null) {
                return (int)$cols;
            }
        }
        return $default;
    }

    public static function wordWrap($string) {
        echo wordwrap($string, self::width(80));
    }
}