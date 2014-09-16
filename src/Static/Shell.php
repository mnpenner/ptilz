<?php
namespace Ptilz;

use Ptilz\Exceptions\ArgumentTypeException;

/**
 * Shell helper methods.
 */
abstract class Shell {
    /**
     * Escape a command and its arguments
     *
     * @param string|array|\Traversable $cmd Shell command
     * @param array $args Command arguments
     * @throws Exceptions\ArgumentTypeException
     * @return string
     */
    public static function escape($cmd, $args=[]) {
        $cmdArr = [escapeshellcmd($cmd)];
        if($args) {
            if(!Iter::isIterable($args)) {
                throw new ArgumentTypeException('args', 'array|Traversable');
            }
            foreach($args as $k => $v) {
                if(is_int($k)) {
                    $cmdArr[] = escapeshellarg($v);
                } elseif($v !== false) {
                    // there's no standard, so who knows what format we should use?
                    if(strlen($k) === 1) {
                        $cmdArr[] = '-' . escapeshellarg($k);
                    } else {
                        $cmdArr[] = '--' . escapeshellarg($k);
                    }
                    if(!in_array($v, [true, null], true)) {
                        $cmdArr[] = escapeshellarg($v);
                    }
                }
            }
        }
        return implode(' ', $cmdArr);
    }

    /**
     * Execute an external program and display raw output
     *
     * @param string|array $cmd
     * @param $args
     * @throws Exceptions\ArgumentTypeException
     * @return mixed
     */
    public static function passthru($cmd, $args=[]) {
        passthru(self::escape($cmd, $args), $return_var);
        return $return_var;
    }

    /**
     * Execute command via shell and return the output as a string.
     *
     * @param string|array $cmd
     * @param $args
     * @throws Exceptions\ArgumentTypeException
     * @return string The output from the executed command. Trailing newlines are stripped.
     */
    public static function exec($cmd, $args=[]) {
        return rtrim(shell_exec(self::escape($cmd, $args)), "\n\r");
    }

    /**
     * Execute an external program and return its exit code.
     *
     * @param string|array $cmd
     * @param array $args
     * @param string $stdout
     * @param string $stderr
     * @throws ArgumentTypeException
     * @return int
     */
    public static function status($cmd, $args=[], &$stdout=null, &$stderr=null) {
        // TODO: change remaining args to ...$pipes
        $proc = proc_open(self::escape($cmd, $args),[
            1 => ['pipe','w'],
            2 => ['pipe','w'],
        ],$pipes);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        return proc_close($proc);
    }

    /**
     * Execute a command in the background.
     *
     * @param $cmd
     * @param array $args
     */
    public static function bg($cmd, $args = []) {
        $cmdStr = self::escape($cmd, $args);
        if(Env::isWindows()) {
            pclose(popen('start /B ' . $cmdStr, 'r'));
        } else {
            exec($cmdStr . ' > /dev/null &');
        }
    }
}