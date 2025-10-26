<?php
use Ptilz\Cli;
use Ptilz\Path;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase {

    function testWidth() {
        $width = Cli::width(0);
        $this->assertTrue(is_int($width) && $width > 0);
    }
}