<?php
namespace Ptilz;
use Ptilz\Exceptions\NotImplementedException;

/**
 * Command-line methods
 */
abstract class Cli {

    public static function write($format) {
        echo self::colorize(call_user_func_array(['\Ptilz\Str', 'format'], func_get_args()));
    }

    public static function writeLine($format) {
        echo self::colorize(call_user_func_array(['\Ptilz\Str', 'format'], func_get_args())) . PHP_EOL;
    }

    public static function colorize($str) {
        return Str::replace([
            '<b>' => "\033[1m",
            '<bold>' => "\033[1m",
            '<bright>' => "\033[1m",
            '<strong>' => "\033[1m",
            '</b>' => "\033[2m", // '21' should work, but it doesn't...?
            '</bold>' => "\033[2m",
            '</bright>' => "\033[2m",
            '</strong>' => "\033[2m",

            '<d>' => "\033[2m",
            '<dim>' => "\033[2m",
            '</d>' => "\033[22m",
            '</dim>' => "\033[22m",

            '<u>' => "\033[4m",
            '<underline>' => "\033[4m",
            '<underscore>' => "\033[4m",
            '</u>' => "\033[24m",
            '</underline>' => "\033[24m",
            '</underscore>' => "\033[24m",

            '<blink>' => "\033[5m",
            '</blink>' => "\033[25m",

            '<i>' => "\033[7m",
            '<inverse>' => "\033[7m",
            '<reverse>' => "\033[7m",
            '</i>' => "\033[27m",
            '</inverse>' => "\033[27m",
            '</reverse>' => "\033[27m",

            '<hidden>' => "\033[8m",
            '</hidden>' => "\033[28m",
            '<conceal>' => "\033[8m",
            '</conceal>' => "\033[28m",

            '<fg:black>' => "\033[30m",
            '<fg:red>' => "\033[31m",
            '<fg:green>' => "\033[32m",
            '<fg:yellow>' => "\033[33m",
            '<fg:blue>' => "\033[34m",
            '<fg:magenta>' => "\033[35m",
            '<fg:cyan>' => "\033[36m",
            '<fg:lgray>' => "\033[37m",
            '<fg:lgrey>' => "\033[37m",
            '<fg:dgray>' => "\033[90m",
            '<fg:dgrey>' => "\033[90m",
            '<fg:lred>' => "\033[91m",
            '<fg:lgreen>' => "\033[92m",
            '<fg:lyellow>' => "\033[93m",
            '<fg:lblue>' => "\033[94m",
            '<fg:lmagenta>' => "\033[95m",
            '<fg:lcyan>' => "\033[96m",
            '<fg:white>' => "\033[97m",
            '<fg:default>' => "\033[39m",
            '</fg>' => "\033[39m",

            '<reset>' => "\033[0m",
            '<clear>' => "\033[0m",
            '<default>' => "\033[0m",

            '</bg>' => "\033[49m",
            '<bg:default>' => "\033[49m",
            '<bg:black>' => "\033[40m",
            '<bg:red>' => "\033[41m",
            '<bg:green>' => "\033[42m",
            '<bg:yellow>' => "\033[43m",
            '<bg:blue>' => "\033[44m",
            '<bg:magenta>' => "\033[45m",
            '<bg:cyan>' => "\033[46m",
            '<bg:lgray>' => "\033[47m",
            '<bg:lgrey>' => "\033[47m",
            '<bg:dgray>' => "\033[100m",
            '<bg:dgrey>' => "\033[100m",
            '<bg:lred>' => "\033[101m",
            '<bg:lgreen>' => "\033[102m",
            '<bg:lyellow>' => "\033[103m",
            '<bg:lblue>' => "\033[104m",
            '<bg:lmagenta>' => "\033[105m",
            '<bg:lcyan>' => "\033[106m",
            '<bg:white>' => "\033[107m",
        ],$str);
    }

    /**
     * Prints an array of strings in columns in order to fit nicely within a terminal window.
     *
     * @param string[] $items
     * @param int $maxWidth Maximum width, in chars
     */
    public static function printColumns($items, $maxWidth=null) {
        if($maxWidth === null) $maxWidth = self::width(80);

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
    public static function width($default = null) {
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

    public static function wordWrap($string, $width=null) {
        if($width === null) $width = self::width(80);
        echo wordwrap($string, $width);
    }
}