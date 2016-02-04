<?php
use Ptilz\V;

class VarTest extends PHPUnit_Framework_TestCase {
    function testToString() {
        $this->assertSame('0',V::toString(0));
        $this->assertSame('0.',V::toString(0.0));
        $this->assertSame('[1,2,3]',V::toString([1,2,3]));
        $this->assertSame('{a:1,2:"b"}',V::toString(['a'=>1,2=>'b']));
        $this->assertSame('b16,077F', V::toString("\x07\x7F"));
        $this->assertSame('Debuggable_1454626696{x:1,y:"a"}', V::toString(new Debuggable_1454626696));
        // todo: add some more tests
    }

    function testIsTruthy() {
        $this->assertTrue(V::isTruthy(true));
        $this->assertTrue(V::isTruthy(1));
        $this->assertTrue(V::isTruthy('0'));
        $this->assertTrue(V::isTruthy(['foo']));
        $this->assertTrue(V::isTruthy(['']));
        $this->assertTrue(V::isTruthy([null]));
        $this->assertFalse(V::isTruthy(0.0));
        $this->assertTrue(V::isTruthy(0.0000001));

        $this->assertFalse(V::isTruthy(false));
        $this->assertFalse(V::isTruthy([]));
        $this->assertFalse(V::isTruthy(''));
        $this->assertFalse(V::isTruthy(0));
        $this->assertFalse(V::isTruthy(null));
    }

    function testIsFalsey() {
        $this->assertFalse(V::isFalsey(true));
        $this->assertFalse(V::isFalsey(1));
        $this->assertFalse(V::isFalsey('0'));
        $this->assertFalse(V::isFalsey(['foo']));
        $this->assertFalse(V::isFalsey(['']));
        $this->assertFalse(V::isFalsey([null]));
        $this->assertTrue(V::isFalsey(0.0));
        $this->assertFalse(V::isFalsey(0.0000001));

        $this->assertTrue(V::isFalsey(false));
        $this->assertTrue(V::isFalsey([]));
        $this->assertTrue(V::isFalsey(''));
        $this->assertTrue(V::isFalsey(0));
        $this->assertTrue(V::isFalsey(null));
    }

    function testIsEmpty() {
        $this->assertTrue(V::isEmpty([]));
        $this->assertTrue(V::isEmpty(''));
        $this->assertTrue(V::isEmpty(new Countable_1454626667(0)));

        $this->assertFalse(V::isEmpty(0));
        $this->assertFalse(V::isEmpty('0'));
        $this->assertFalse(V::isEmpty(1));
        $this->assertFalse(V::isEmpty(-1));
        $this->assertFalse(V::isEmpty(0.0000001));
        $this->assertFalse(V::isEmpty(new Countable_1454626667(1)));
    }
}

class Debuggable_1454626696 {
    function __debugInfo() {
        return [
            'x' => 1,
            'y' => 'a',
        ];
    }
}

class Countable_1454626667 implements Countable {
    /** @var int */
    private $count;

    public function __construct($count) {
        $this->count = $count;
    }


    public function count() {
        return $this->count;
    }
}