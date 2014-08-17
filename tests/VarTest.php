<?php
use Ptilz\V;

class VarTest extends PHPUnit_Framework_TestCase {
    function testToString() {
        $this->assertSame('0',V::toString(0));
        $this->assertSame('0.0',V::toString(0.0));
        $this->assertSame('[1,2,3]',V::toString([1,2,3]));
        $this->assertSame('{a:1,2:"b"}',V::toString(['a'=>1,2=>'b']));
        $this->assertSame('b16,077F', V::toString("\x07\x7F"));
    }
}