<?php
use Ptilz\Shell;

class ShellTest extends PHPUnit_Framework_TestCase {
    function testCmdExists() {
        $this->assertTrue(Shell::cmdExists('cd'), "cd");
        $this->assertTrue(Shell::cmdExists('phpunit'), "phpunit");
        $this->assertFalse(Shell::cmdExists('Dpzqjcmi1yX8qULqzANUd20IIWU9w1A4UlXOAnPn'), "random (not-existent) command");
    }

    function testEscapeArgs() {
        $this->assertSame(' foo bar f b', Shell::escapeArgs(['foo', 'bar', 'f', 'b']));
        $this->assertSame(" '--foo' '-b'", Shell::escapeArgs(['--foo', '-b']));
        $this->assertSame(' --foo -b', Shell::escapeArgs(['foo' => true, 'bar' => false, 'f' => false, 'b' => true]));
        $this->assertSame('', Shell::escapeArgs([]));
        $this->assertSame(' compress --mangle sort', Shell::escapeArgs(['compress', 'mangle' => 'sort']));
        $this->assertSame(" -ofoo.min.js --source-map foo.min.js.map --compress 'sequences,properties=true,dead_code=false' -p5 -c", Shell::escapeArgs(['o' => 'foo.min.js', 'source-map' => 'foo.min.js.map', 'compress' => [
            'sequences',
            'properties' => true,
            'dead_code' => false,
        ], 'p' => 5, 'c' => true, 'm' => false]));
    }

    function testEscape() {
        $this->assertSame('uglifyjs', Shell::escape('uglifyjs'));
        $this->assertSame("uglifyjs -ofoo.min.js --source-map foo.min.js.map --compress 'sequences,properties=true,dead_code=false' -p5 -c", Shell::escape('uglifyjs', ['o' => 'foo.min.js', 'source-map' => 'foo.min.js.map', 'compress' => [
            'sequences',
            'properties' => true,
            'dead_code' => false,
        ], 'p' => 5, 'c' => true, 'm' => false]));
    }
}