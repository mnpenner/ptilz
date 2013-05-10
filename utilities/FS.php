<?php

class FS {
    public static function joinPaths() {
        return preg_replace('~[/\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, func_get_args()));
    }
}