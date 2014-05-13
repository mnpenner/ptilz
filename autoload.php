<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'utilities' . DIRECTORY_SEPARATOR . 'Path.php';

if(function_exists('__autoload')) {
    spl_autoload_register('__autoload');
}

set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    Path::join(__DIR__, 'callbacks'),
    Path::join(__DIR__, 'collections'),
    Path::join(__DIR__, 'dombuilder'),
    Path::join(__DIR__, 'exceptions'),
    Path::join(__DIR__, 'misc'),
    Path::join(__DIR__, 'utilities'),
)));

spl_autoload_extensions('.php');
spl_autoload_register();