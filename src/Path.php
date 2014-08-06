<?php
namespace Ptilz;
use Exception;
use Ptilz\Exceptions\ArgumentEmptyException;

abstract class Path {
    /** @var bool Is Windows OS */
    private static $_isWin;
    /** @var string Path separator */
    private static $_sep;

    /**
     * Join all arguments together with the directory separator and normalize.
     *
     * @return string Normalized path
     */
    public static function join() {
        return self::normalize(implode(self::$_sep, func_get_args()));
    }

    /**
     * Resolves an absolute path.
     *
     * @throws Exception
     * @return string
     * @see http://nodejs.org/api/path.html#path_path_resolve_from_to
     */
    public static function resolve() {
        $working_dir = getcwd();
        foreach(func_get_args() as $p) {
            if($p === null || $p === '') continue;
            elseif(self::isAbsolute($p)) $working_dir = $p;
            else $working_dir .= self::$_sep . $p;
        }
        return self::normalize($working_dir);
    }

    /**
     * Determines if a file path is absolute.
     *
     * @param string $path
     * @return bool
     * @throws Exceptions\ArgumentEmptyException
     */
    public static function isAbsolute($path) {
        // translated from https://github.com/filearts/node-absolute-path/blob/184265b630bac9ff034a22c9dfcad5a0a68f332a/index.js#L3-L18
        if(Str::isEmpty($path)) throw new ArgumentEmptyException('path');
        if(!self::$_isWin) {
            return $path[0] === '/';
        }
        if(!preg_match('~([a-zA-Z]:|[\\\/]{2}[^\\\/]+[\\\/]+[^\\\/]+)?([\\\/])?([\s\S]*?)\z~A', $path, $m)) {
            return false;
        }
        $device = Arr::get($m, 1, '');
        $isUnc = $device && $device[1] !== ':';
        return $m[2] || $isUnc;
    }

    /**
     * Normalize a string path, taking care of '..' and '.' parts.
     *
     * @param string $path
     * @return string
     * @see http://nodejs.org/api/path.html#path_path_normalize_p
     */
    public static function normalize($path) {
        $path = preg_replace('~[/\\\\]+~', self::$_sep, $path);
        if($path === self::$_sep) return $path;
        $out = [];
        foreach(explode(self::$_sep, rtrim($path, self::$_sep)) as $p) {
            if($p === '.') continue;
            if($p === '..') array_pop($out);
            else $out[] = $p;
        }
        return implode(self::$_sep, $out);
    }


    /**
     * Solve the relative path from from to to.
     *
     * @param $from
     * @param $to
     * @return string
     * @see http://nodejs.org/api/path.html#path_path_relative_from_to
     */
    public static function relative($from, $to) {
        $from_parts = explode(self::$_sep, ltrim(self::resolve($from), self::$_sep));
        $to_parts = explode(self::$_sep, ltrim(self::resolve($to), self::$_sep));

        while($from_parts && $to_parts && $from_parts[0] === $to_parts[0]) {
            array_shift($from_parts);
            array_shift($to_parts);
        }

        return str_repeat('..' . self::$_sep, count($from_parts)) . implode(self::$_sep, $to_parts);
    }

    public static function __initStatic() {
        self::$_isWin = Env::isWindows();
        self::$_sep = DIRECTORY_SEPARATOR;
    }

    /**
     * Sets the platform to Windows for testing
     *
     * @param bool $flag True for Windows, false for any other OS
     */
    public static function setWindows($flag) {
        self::$_isWin = (bool)$flag;
        self::$_sep = self::$_isWin ? '\\' : '/';
    }

    /**
     * Get the user's home directory.
     * @return string
     */
    public static function home(){
        // todo: needs testing on windows
        return getenv('HOME');
    }

    public static function parentDirectory($path = null) {
        if($path === null) $path = getcwd();
        return self::join($path, '..');
    }
}

Path::__initStatic();