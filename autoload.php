<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'utilities' . DIRECTORY_SEPARATOR . 'FS.php';

if(function_exists('__autoload')) {
    spl_autoload_register('__autoload');
}

set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    FS::joinPaths(__DIR__, 'callbacks'),
    FS::joinPaths(__DIR__, 'collections'),
    FS::joinPaths(__DIR__, 'dombuilder'),
    FS::joinPaths(__DIR__, 'exceptions'),
    FS::joinPaths(__DIR__, 'misc'),
    FS::joinPaths(__DIR__, 'utilities'),
)));

spl_autoload_extensions('.php');
spl_autoload_register();