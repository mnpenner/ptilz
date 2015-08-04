<?php
namespace Ptilz;
use Ptilz\Exceptions\NotImplementedException;

/**
 * Command-line methods
 */
abstract class Cli { // fixme: rename to Console:: ? Or Term::?

    public static function write($format) {
        $args = func_get_args();
        $args[0] = self::colorize($args[0]);
        echo call_user_func_array([Str::class, 'format'], $args);
    }

    public static function writeLine($format) {
        $args = func_get_args();
        $args[0] = self::colorize($args[0]);
        echo call_user_func_array([Str::class, 'format'], $args) . PHP_EOL;
    }

    /**
     * Converts HTML-like tags into ANSI/VT100 control sequences and decodes HTML entities.
     *
     * @param string $str
     * @return mixed
     */
    public static function colorize($str) {
        // see http://misc.flogisoft.com/bash/tip_colors_and_formatting
        // http://en.wikipedia.org/wiki/ANSI_escape_code#CSI_codes
        $replaceMatch = function($m) {
            $codes = [];
            if($m[0] === '/') {
                $attrs = explode(';',substr($m,1));
                foreach($attrs as $attr) {
                    switch(strtok($attr,':')) {
                        case 'fg': $codes[] = 39; break;
                        case 'bg': $codes[] = 49; break;
                        case 'all': $codes[] = 0; break;
                        case 'b': $codes[] = 22; break;
                        case 'bright': $codes[] = 22; break;
                        case 'bold': $codes[] = 22; break;
                        case 'dim': $codes[] = 22; break;
                        case 'i': $codes[] = 23; break;
                        case 'italic': $codes[] = 23; break;
                        case 'fraktur': $codes[] = 23; break;
                        case 'u': $codes[] = 24; break;
                        case 'underline': $codes[] = 24; break;
                        case 'blink': $codes[] = 25; break;
                        case 'blink-slow': $codes[] = 25; break;
                        case 'blink-rapid': $codes[] = 25; break;
                        case 'inverse': $codes[] = 27; break;
                        case 'negative': $codes[] = 27; break;
                        case 'highlight': $codes[] = 27; break;
                        case 'reverse': $codes[] = 27; break;
                        case 'hidden': $codes[] = 28; break;
                        case 'conceal': $codes[] = 28; break;
                        case 'strike': $codes[] = 29; break;
                        case 's': $codes[] = 29; break;
                        case 'del': $codes[] = 29; break;
                        case 'framed': $codes[] = 54; break;
                        case 'encircled': $codes[] = 54; break;
                        case 'overlined': $codes[] = 55; break;
                        default: return null;
                    }
                }
            } else {
                $attrs = explode(';',$m);
                foreach($attrs as $attr) {
                    if(strpos($attr,':') !== false) {
                        list($ground, $colorName) = explode(':',$attr,2);
                        if(preg_match('~\d+\z~A',$colorName)) {
                            switch($ground) {
                                case 'fg': $codes[] = 38; break;
                                case 'bg': $codes[] = 48; break;
                                default: return null;
                            }
                            $codes[] = 5;
                            $codes[] = $colorName;
                        } else {
                            switch($ground) {
                                case 'fg': $colorNumber = 30; break;
                                case 'bg': $colorNumber = 40; break;
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
                                case 'grey': $colorNumber += 7; break;
                                case 'gray' :$colorNumber += 7; break;
                                case 'bright-grey': $colorNumber += 7; break;
                                case 'bright-gray': $colorNumber += 7; break;
                                case 'light-grey': $colorNumber += 7; break;
                                case 'light-gray': $colorNumber += 7; break;
                                case 'default': $colorNumber += 9; break;
                                case 'dark-grey': $colorNumber += 60; break;
                                case 'dark-gray': $colorNumber += 60; break;
                                case 'bright-red': $colorNumber += 61; break;
                                case 'light-red': $colorNumber += 61; break;
                                case 'bright-green': $colorNumber += 62; break;
                                case 'light-green': $colorNumber += 62; break;
                                case 'bright-yellow': $colorNumber += 63; break;
                                case 'light-yellow': $colorNumber += 63; break;
                                case 'bright-blue': $colorNumber += 64; break;
                                case 'light-blue': $colorNumber += 64; break;
                                case 'bright-magenta': $colorNumber += 65; break;
                                case 'light-magenta': $colorNumber += 65; break;
                                case 'bright-cyan': $colorNumber += 66; break;
                                case 'light-cyan': $colorNumber += 66; break;
                                case 'white': $colorNumber += 67; break;
                                default: return null;
                            }
                            $codes[] = $colorNumber;
                        }
                    } else {
                        switch($attr) {
                            case 'reset': $codes[] = 0; break;
                            case 'normal': $codes[] = 0; break;
                            case 'default': $codes[] = 0; break;
                            case 'b': $codes[] = 1; break;
                            case 'bold': $codes[] = 1; break;
                            case 'bright': $codes[] = 1; break;
                            case 'dim': $codes[] = 2; break;
                            case 'i': $codes[] = 3; break;
                            case 'italic': $codes[] = 3; break;
                            case 'u': $codes[] = 4; break;
                            case 'underline': $codes[] = 4; break;
                            case 'blink-slow': $codes[] = 5; break;
                            case 'blink-rapid': $codes[] = 6; break;
                            case 'inverse': $codes[] = 7; break;
                            case 'negative': $codes[] = 7; break;
                            case 'reverse': $codes[] = 7; break;
                            case 'highlight': $codes[] = 7; break;
                            case 'hidden': $codes[] = 8; break;
                            case 'conceal': $codes[] = 8; break;
                            case 'strike': $codes[] = 9; break;
                            case 's': $codes[] = 9; break;
                            case 'del': $codes[] = 9; break;
                            case 'primary': $codes[] = 10; break;
                            case 'fraktur': $codes[] = 20; break;
                            case 'framed': $codes[] = 51; break;
                            case 'encircled': $codes[] = 52; break;
                            case 'overlined': $codes[] = 53; break;
                            default: return null;
                        }
                    }
                }
            }
            return implode(';',$codes);
        };

        return htmlspecialchars_decode(preg_replace_callback('~<(?<tag>/?[a-z0-9:;-]+)>~', function ($m) use ($replaceMatch) {
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
        $triedChunkSizes = [];
        for($nColumns = 2; $nColumns <= count($items); ++$nColumns) {
            $chunkSize = (int)ceil(count($items) / $nColumns);
            if(isset($triedChunkSizes[$chunkSize])) continue;
            $triedChunkSizes[$chunkSize] = true;
            $testChunks = array_chunk($items, $chunkSize);
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