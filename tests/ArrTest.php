<?php
use Ptilz\Arr;

class PtilzTest extends PHPUnit_Framework_TestCase {
    function testGet() {
        $arr = ['a' => 1, 2 => 'b'];
        $this->assertEquals(1, Arr::get($arr, 'a'));
        $this->assertEquals(1, Arr::get($arr, 'a', 'x'));
        $this->assertEquals('b', Arr::get($arr, 2, 'x'));
        $this->assertEquals('x', Arr::get($arr, 3, 'x'));
    }
}
 