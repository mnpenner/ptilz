<?php


echo getenv("username").PHP_EOL;

//var_dump(posix_geteuid());

//var_dump(getenv());

$obj = new COM("wscript.network");
echo $obj->username.PHP_EOL;

exit(PHP_EOL);