<?php

use Ptilz\Collections\Set;

class SetTest extends PHPUnit_Framework_TestCase {

    function testConstruct() {
        $s = new Set([2,3,4]);
        $this->assertSame([2,3,4],$s->toArray());

        $s = new Set([4,5,6,4]);
        $this->assertSame([4,5,6],$s->toArray());
    }

    function testContains() {
        $s = new Set([2,3,4]);

        $this->assertTrue($s->contains(3));
        $this->assertFalse($s->contains(5));
    }

    function testAdd() {
        $s = new Set([2,3,4]);
        $s->add(4,5);
        $this->assertSame([2,3,4,5],$s->toArray());
    }

    function testAddRange() {
        $s = new Set([2,3,4]);
        $s->addRange([4,5],[3,6]);
        $this->assertSame([2,3,4,5,6],$s->toArray());
    }

    function testIntersect() {
        $s1 = new Set([2,3,4]);
        $s2 = new Set([3,4,5]);
        $this->assertSame([3,4],$s1->intersect($s2)->toArray());
    }

    function testUnion() {
        $s1 = new Set([2,3,4]);
        $s2 = new Set([3,4,5]);
        $this->assertSame([2,3,4,5],$s1->union($s2)->toArray());
    }

    function testUnionWith() {
        $s1 = new Set([2,3,4]);
        $s2 = new Set([3,4,5]);
        $s1->unionWith($s2);
        $this->assertSame([2,3,4,5],$s1->toArray());
    }

    function testCount() {
        $s1 = new Set([1,2,3,4]);
        $this->assertSame(4,$s1->count());

        $s2 = new Set();
        $this->assertSame(0,$s2->count());
    }
}