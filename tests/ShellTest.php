<?php
use Ptilz\Shell;

class ShellTest extends PHPUnit_Framework_TestCase {
    function testCmdExists() {
        $this->assertTrue(Shell::cmdExists('phpunit'));
    }
}