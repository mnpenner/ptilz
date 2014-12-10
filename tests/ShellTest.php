<?php
use Ptilz\Shell;

class ShellTest extends PHPUnit_Framework_TestCase {
    function testCmdExists() {
        $this->assertTrue(Shell::cmdExists('cd'),"cd");
        $this->assertTrue(Shell::cmdExists('phpunit'),"phpunit");
        $this->assertFalse(Shell::cmdExists('Dpzqjcmi1yX8qULqzANUd20IIWU9w1A4UlXOAnPn'),"random (not-existent) command");
    }
}