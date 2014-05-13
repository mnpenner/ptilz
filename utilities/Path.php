<?php

class Path {
    public static function join() {
        return self::normalize(implode(DIRECTORY_SEPARATOR, func_get_args()));
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
            else $working_dir .= DIRECTORY_SEPARATOR.$p;
        }
        return self::normalize($working_dir);
    }

    public static function isAbsolute($path) {
        if($path === null || $path === '') throw new Exception("Empty path");
        return $path[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i',$path) > 0;
    }

    /**
     * Normalize a string path, taking care of '..' and '.' parts.
     *
     * @param $path
     * @return string
     * @see http://nodejs.org/api/path.html#path_path_normalize_p
     */
    public static function normalize($path) {
        $path = preg_replace('~[/\\\\]+~',DIRECTORY_SEPARATOR, $path);
        if($path === DIRECTORY_SEPARATOR) return $path;
        $out = [];
        foreach(explode(DIRECTORY_SEPARATOR,rtrim($path,DIRECTORY_SEPARATOR)) as $p) {
            if($p === '.') continue;
            if($p === '..') array_pop($out);
            else $out[] = $p;
        }
        return implode(DIRECTORY_SEPARATOR,$out);
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
        $from_parts = explode(DIRECTORY_SEPARATOR, substr(self::resolve($from), 1));
        $to_parts = explode(DIRECTORY_SEPARATOR, substr(self::resolve($to), 1));

        while($from_parts && $to_parts && $from_parts[0] === $to_parts[0]) {
            array_shift($from_parts);
            array_shift($to_parts);
        }

        return str_repeat('..'.DIRECTORY_SEPARATOR,count($from_parts)) . implode(DIRECTORY_SEPARATOR,$to_parts);
    }
}