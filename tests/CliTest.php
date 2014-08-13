<?php
use Ptilz\Cli;
use Ptilz\Path;

class CliTest extends PHPUnit_Framework_TestCase {

    function testWidth() {
        $width = Cli::width();
        $this->assertTrue(is_int($width) && $width > 0);
    }
}