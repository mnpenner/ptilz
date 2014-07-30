<?php
namespace Ptilz;

use Ptilz\Exceptions\ArgumentTypeException;

/**
 * System call helper methods.
 */
abstract class Sys {
    /**
     * Escape a command and its arguments
     *
     * @param string|array|\Traversable $cmd Shell args
     * @throws Exceptions\ArgumentTypeException
     * @return string
     */
    public static function escape($cmd = []) {
        if(is_string($cmd)) {
            return escapeshellarg($cmd);
        }
        if(!Iter::isIterable($cmd)) {
            throw new ArgumentTypeException('cmd', 'string|array|Traversable');
        }
        $cmdArr = [];
        foreach($cmd as $k => $v) {
            if(is_int($k)) {
                $cmdArr[] = escapeshellarg($v);
            } elseif($v !== false) {
                // there's no standard, so who knows what format we should use?
                if(strlen($k) === 1) {
                    $cmdArr[] = '-' . escapeshellarg($k);
                } else {
                    $cmdArr[] = '--' . escapeshellarg($k);
                }
                if(!in_array($v, [true, '', null], true)) {
                    $cmdArr[] = escapeshellarg($v);
                }
            }
        }
        return implode(' ', $cmdArr);
    }

    /**
     * Execute an external program and display raw output
     *
     * @param string|array $cmd
     * @return mixed
     */
    public static function passthru($cmd) {
        passthru(self::escape($cmd), $return_var);
        return $return_var;
    }

    /**
     * Execute command via shell and return the complete output as a string
     *
     * @param string|array $cmd
     * @return string The output from the executed command. Trailing newlines are stripped.
     * @throws Exceptions\ArgumentTypeException
     */
    public static function exec($cmd) {
        return rtrim(shell_exec(self::escape($cmd)), "\n\r");
    }
}