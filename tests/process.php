#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

$timer = microtime(true);
$composerInfo = new \Ptilz\ProgExec\Process('composer info');
$npmInfo = new \Ptilz\ProgExec\Process('npm ls --depth=0 --long --json');
dump(microtime(true) - $timer);

$timer = microtime(true);
$composerInfo->stdout->read();
dump(microtime(true) - $timer);

$timer = microtime(true);
$npmInfo->stdout->read();
dump(microtime(true) - $timer);


//$composerInfo->close();
//$npmInfo->close();