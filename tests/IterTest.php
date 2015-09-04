<?php
use Ptilz\Iter;

class IterTest extends PHPUnit_Framework_TestCase {
    function testToArray() {
        $generator = function () {
            yield 1;
            yield 'b' => 2;
        };
        $this->assertSame([1, 'b' => 2], Iter::toArray($generator()));

        $array = ['recipe' => 'pancakes', 'egg', 'milk', 'flour'];
        $iterator = new ArrayIterator($array);

        $this->assertSame($array, Iter::toArray($array));
        $this->assertSame($array, Iter::toArray($iterator));
        $this->assertSame(array_values($array), Iter::toArray($iterator, false));
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

    function testAll() {
        $this->assertTrue(Iter::all([1,2,'a','0','b',['c']]));
        $this->assertFalse(Iter::all([1,2,'a',0,'b',['c']]));
        $this->assertTrue(Iter::all([1,'2',3.14,'4e5','0xDEADBEEF'],'is_numeric'));
    }

    function testAny() {
        $this->assertTrue(Iter::any([0,false,true,null]));
        $this->assertFalse(Iter::any([0,false,[]]));
        $this->assertFalse(Iter::any(['what','does','the','fox','say'],'is_numeric'));
        $this->assertTrue(Iter::any(['yip','yiiiip','0xcafe'],'is_numeric'));
    }

    function testCountable() {
        $this->assertTrue(Iter::isCountable([]));
        $this->assertTrue(Iter::isCountable(new _Countable));
        $this->assertFalse(Iter::isCountable(5));
        $this->assertFalse(Iter::isCountable('foo'));
    }
}

class _Countable implements Countable {
    public function count() {
        return 1;
    }
}