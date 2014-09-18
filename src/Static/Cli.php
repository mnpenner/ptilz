<?php
namespace Ptilz;
use Ptilz\Exceptions\NotImplementedException;

/**
 * Command-line methods
 */
abstract class Cli {

    public static function write($format) {
        echo call_user_func_array(['Str', 'format'], func_get_args());
    }

    public static function writeLine($format) {
        echo call_user_func_array(['Str', 'format'], func_get_args()) . PHP_EOL;
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
            if(preg_match('/CON.*:(\n[^|]+?){3}(?<cols>\d+)/', `mode`, $matches)) {
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