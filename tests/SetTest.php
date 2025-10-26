<?php

use Ptilz\Collections\Set;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase {

    /**
     * @covers \Ptilz\Collections\Set::__construct
     * @covers \Ptilz\Collections\Set::toArray
     */
    function testConstruct() {
        $s1 = new Set([2,3,4]);
        $this->assertSame([2,3,4],$s1->toArray());

        $s2 = new Set([4,5,6,4]);
        $this->assertSame([4,5,6],$s2->toArray());

        $s3 = new Set(self::it());
        $this->assertSame([2,4,6],$s3->toArray());

        $s4 = new Set($s3);
        $this->assertSame([2,4,6],$s4->toArray());
    }
    
    private static function it() {
        yield 2;
        yield 4;
        yield 6;
    }

    /**
     * @covers \Ptilz\Collections\Set::contains
     */
    function testContains() {
        $s = new Set([2,3,4]);

        $this->assertTrue($s->contains(3));
        $this->assertFalse($s->contains(5));
    }

    /**
     * @covers \Ptilz\Collections\Set::add
     */
    function testAdd() {
        $s = new Set([2,3,4]);
        $s->add(4,5);
        $this->assertSame([2,3,4,5],$s->toArray());
    }

    /**
     * @covers \Ptilz\Collections\Set::addRange
     */
    function testAddRange() {
        $s = new Set([2,3,4]);
        $s->addRange([4,5],[3,6]);
        $this->assertSame([2,3,4,5,6],$s->toArray());
    }

    /**
     * @covers \Ptilz\Collections\Set::intersect
     */
    function testIntersect() {
        $s1 = new Set([2,3,4]);
        $s2 = new Set([3,4,5]);
        $this->assertSame([3,4],$s1->intersect($s2)->toArray());

        $s1 = new Set([2,3,4]);
        $s2 = [3,4,5];
        $this->assertSame([3,4],$s1->intersect($s2)->toArray());
    }

    /**
     * @covers \Ptilz\Collections\Set::union
     */
    function testUnion() {
        $s1 = new Set([2,3,4]);
        $s2 = new Set([3,4,5]);
        $this->assertSame([2,3,4,5],$s1->union($s2)->toArray());

        $s1 = new Set([2,3,4]);
        $s2 = [3,4,5];
        $this->assertSame([2,3,4,5],$s1->union($s2)->toArray());
    }

    /**
     * @covers \Ptilz\Collections\Set::unionWith
     */
    function testUnionWith() {
        $s1 = new Set([2,3,4]);
        $s2 = new Set([3,4,5]);
        $s1->unionWith($s2);
        $this->assertSame([2,3,4,5],$s1->toArray());

        $s1 = new Set([2,3,4]);
        $s2 = [3,4,5];
        $s1->unionWith($s2);
        $this->assertSame([2,3,4,5],$s1->toArray());
    }

    /**
     * @covers \Ptilz\Collections\Set::count
     */
    function testCount() {
        $s1 = new Set([1,2,3,4]);
        $this->assertSame(4,$s1->count());

        $s2 = new Set();
        $this->assertSame(0,$s2->count());
    }

    /**
     * @covers \Ptilz\Collections\Set::remove
     */
    function testRemove() {
        $s1 = new Set([1,2,3,4]);
        $s1->remove(2,4);
        $this->assertSame([1,3],$s1->toArray());
    }

    /**
     * @covers \Ptilz\Collections\Set::getIterator
     */
    function testGetIterator() {
        $s = new Set([1,2,3]);
        $this->assertInstanceOf(\Traversable::class, $s->getIterator());
    }
}