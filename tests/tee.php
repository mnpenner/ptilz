#!/usr/bin/env php
<?php
use Ptilz\Shell;
require __DIR__ . '/../vendor/autoload.php';


// Shell::tee("echo 'hello'");
// Shell::tee("echo 'hello'", $stdout);
// $result = Shell::tee("echo 'hello'; (>&2 echo \"error\"); echo 'world' && sleep 2 && echo 'zzz'; false", $stdout, $stderr);
// dump($stdout);
// dump($stderr);
// dump($result);


$result = Shell::tee('cd /usr/local/webenginex && hg merge default', $stdout, $stderr, $std3, $std4);
dump($stdout);
dump($stderr);
dump($std3);
dump($result);
