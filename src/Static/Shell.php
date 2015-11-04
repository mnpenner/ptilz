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
        return escapeshellcmd($cmd).self::escapeArgs($args);
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
     * @param string[] $stdouts
     * @return int
     */
    public static function status($cmd, $args=[], &...$stdouts) {
        $proc = proc_open(self::escape($cmd, $args), array_fill(1, count($stdouts), ['pipe', 'w']), $pipes);
        if($pipes) {
            $p = 1;
            foreach($stdouts as &$out) {
                $out = stream_get_contents($pipes[$p]);
                fclose($pipes[$p]);
                ++$p;
            } unset($out);
        }
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

    /**
     * Silently execute a command
     *
     * @param string $cmd
     * @return bool True if the command returns exit code 0, false otherwise
     */
    public static function tryExec($cmd) {
        $devNull = Path::devNull();
        $proc = proc_open($cmd, [
            1 => ['file', $devNull, 'w'], // suppress output to STDOUT
            2 => ['file', $devNull, 'w'], // suppress output to STDERR
        ], $pipes);
        foreach($pipes as $p) fclose($p); // "If you have open pipes to that process, you should fclose() them prior to calling [proc_close] in order to avoid a deadlock - the child process may not be able to exit while the pipes are open."
        return proc_close($proc) === 0;
    }

    /**
     * Checks if a command exists.
     *
     * @param string $cmd
     * @return bool
     */
    public static function cmdExists($cmd) {
        $arg = escapeshellarg($cmd);
        if(Env::isWindows()) {
            return self::tryExec("(help $arg || exit 0) && where $arg"); // http://stackoverflow.com/a/27394096/65387
        }
        return self::tryExec("command -v $arg"); // http://stackoverflow.com/a/677212/65387
    }

    /**
     * Builds a properly escaped string of shell args.
     *
     * Returns an empty string if no args provided, otherwise begins with a space followed by the escaped args.
     *
     * @param array $args
     * @return string
     * @throws ArgumentTypeException
     */
    public static function escapeArgs($args) {
        if(!Iter::isIterable($args)) {
            throw new ArgumentTypeException('args', 'array|Traversable');
        }
        $out = [];
        foreach($args as $opt => $val) {
            if(is_int($opt)) {
                $out[] = self::escapeArg($val);
            } elseif($val !== false) {
                $shortOpt = strlen($opt) === 1;
                $arg = ($shortOpt ? '-' : '--').self::escapeArg($opt);
                if($val !== true && $val !== null) {
                    if(is_array($val)) {
                        $arg .= self::escapeSubArg($val);
                    } else {
                        if(!$shortOpt) $arg .= ' ';
                        $arg .= self::escapeArg($val);
                    }
                }
                $out[] = $arg;
            }
        }
        return $out ?  ' '.implode(' ', $out) : '';
    }

    /**
     * Escape a string to be used as a shell argument.
     *
     * @param string $str The argument that will be escaped.
     * @return string The escaped string.
     */
    public static function escapeArg($str) {
        if(preg_match('#[A-Za-z0-9_][A-Za-z0-9_.-]*\z#A',$str)) {
            return $str;
        }
        return escapeshellarg($str);
    }

    /**
     * This copies UglifyJS (https://github.com/mishoo/UglifyJS2) syntax.
     *
     * @param array $args
     * @return string
     */
    private static function escapeSubArg(array $args) {
        $out = [];
        foreach($args as $key => $val) {
            if(is_int($key)) {
                $out[] = $val;
            } elseif($val === true) {
                $out[] = "$key=true";
            } elseif($val === false) {
                $out[] = "$key=false";
            } else {
                $out[] = "$key=$val";
            }
        }
        return $out ?  ' '. self::escapeArg(implode(',', $out)) : '';
    }
}