#!/usr/bin/env php
<?php
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
date_default_timezone_set('America/Vancouver');

require '/usr/local/webenginex/www/autoload.php';

$timer = microtime(true);
$composerInfo = new \Ptilz\ProgExec\Process('composer info');
$npmInfo = new \Ptilz\ProgExec\Process('npm ls --depth=0 --long --json');
dump(microtime(true) - $timer);

$timer = microtime(true);
$composerInfo->stdout->read();
$npmInfo->stdout->read();
dump(microtime(true) - $timer);


//$composerInfo->close();
//$npmInfo->close();