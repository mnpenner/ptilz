<?php
use Ptilz\Iter;

class IterTest extends PHPUnit_Framework_TestCase {
    function testToArray() {
        $generator = function () {
            yield 1;
            yield 'b' => 2;
        };
        $this->assertSame([1, 'b' => 2], Iter::toArray($generator()));
    }

    function testMap() {
        $timesTwo = function ($x) {
            return $x * 2;
        };
        $generator = function () {
            yield 1;
            yield 2;
            yield 3;
        };
        $result = Iter::map($generator(), $timesTwo);
        $this->assertInstanceOf('Generator', $result);
        $this->assertSame([2, 4, 6], Iter::toArray($result));
    }
}