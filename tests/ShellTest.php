<?php
use Ptilz\Shell;

class ShellTest extends PHPUnit_Framework_TestCase {
    function testCmdExists() {
        $this->assertTrue(Shell::cmdExists('cd'));
        $this->assertTrue(Shell::cmdExists('phpunit'));
        $this->assertFalse(Shell::cmdExists('Dpzqjcmi1yX8qULqzANUd20IIWU9w1A4UlXOAnPn'));
    }
}