<?php
namespace Ptilz;
use Exception;
use Ptilz\Exceptions\ArgumentEmptyException;
use Ptilz\Exceptions\InvalidOperationException;

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
        // reference https://github.com/filearts/node-absolute-path/blob/184265b630bac9ff034a22c9dfcad5a0a68f332a/index.js#L3-L18
        // reference http://en.wikipedia.org/wiki/Path_%28computing%29#UNC_in_Windows
        if(Str::isBlank($path)) {
            throw new ArgumentEmptyException('path');
        }
        if(!self::$_isWin) {
            return $path[0] === '/';
        }
        return preg_match('~[a-zA-Z]:~A', $path) === 1 || self::isUncPath($path);
    }

    /**
     * Tests if a path follows the Universal Naming Convention (UNC).
     *
     * UNC =  \\<hostname>\<sharename>[\<objectname>]*
     *
     * @param string $path
     * @return bool
     * @see http://msdn.microsoft.com/en-ca/library/gg465305.aspx
     */
    public static function isUncPath($path) {
        return preg_match('~\\\\(?:\\\\[^\\\\/:*?"<>|]+){2,}\z~A',$path) === 1;
    }

    /**
     * Normalize a string path, taking care of '..' and '.' parts.
     *
     * @param string $path
     * @param string $sep Path separator
     * @return string
     * @see http://nodejs.org/api/path.html#path_path_normalize_p
     */
    public static function normalize($path, $sep=null) {
        if($sep === null) $sep = self::$_sep;
        $path = preg_replace('~[/\\\\]+~', $sep, $path);
        if($path === $sep) return $path; // fixme: what should we do if we're on Windows? this isn't a valid path!
        $out = [];
        $dirs = explode($sep, rtrim($path, $sep));
        $isAbs = self::isAbsolute($path);
        $rootDir = $isAbs ? array_shift($dirs) . $sep : '';
        foreach($dirs as $p) {
            if($p === '.') continue;
            if($p === '..') {
                if($isAbs && !$out) continue;
                if(!$out || end($out) === '..') {
                    $out[] = '..';
                } else {
                    array_pop($out);
                }
            } else {
                $out[] = $p;
            }
        }
        if(!$out) return $isAbs ? $rootDir : '.';
        return $rootDir.implode($sep, $out);
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

    private static function _initStatic() {
        self::$_isWin = Env::isWindows();
        self::$_sep = DIRECTORY_SEPARATOR;
    }

    /**
     * Sets the platform to Windows for testing
     *
     * @param bool $flag True for Windows, false for any other OS
     */
    public static function setWindowsMode($flag) {
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

    /**
     * Gets the directory one level up from the given directory
     * @param string|null $path Defaults to current working directory
     * @return string
     */
    public static function parentDirectory($path = null) {
        if($path === null) $path = getcwd();
        return self::join($path, '..');
    }

    /**
     * The file path of the null device
     * @return string "nul" on Windows, "/dev/null" everywhere else
     */
    public static function devNull() {
        return self::$_isWin ? 'nul' : '/dev/null';
    }
}

Func::invokeMethod(Path::class,'_initStatic');
