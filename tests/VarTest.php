<?php
use Ptilz\V;

class VarTest extends PHPUnit_Framework_TestCase {

    /**
     * @covers \Ptilz\V::toString
     */
    function testToString() {
        $this->assertSame('0',V::toString(0));
        $this->assertSame('0.',V::toString(0.0));
        $this->assertSame('[1,2,3]',V::toString([1,2,3]));
        $this->assertSame('{a:1,2:"b"}',V::toString(['a'=>1,2=>'b']));
        $this->assertSame('b16,077F', V::toString("\x07\x7F"));
        $this->assertSame('Debuggable_1454626696{x:1,y:"a"}', V::toString(new Debuggable_1454626696));
        $this->assertSame('Countable_1454626667', V::toString(new Countable_1454626667(99)));
        // todo: add some more tests
    }

    /**
     * @covers \Ptilz\V::isTruthy
     */
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

    /**
     * @covers \Ptilz\V::isFalsey
     */
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

    /**
     * @covers \Ptilz\V::isEmpty
     */
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
        $this->assertFalse(V::isEmpty(self::gen1()));
        $this->assertTrue(V::isEmpty(self::gen2()));
    }
    
    private static function gen1() {
        yield 1;
    }

    private static function gen2() {
        if(false) {
            yield 1;
        }
    }

    /**
     * @covers \Ptilz\V::coalesce
     */
    function testCoalesce() {
        $this->assertSame(1,V::coalesce(null,false,[],0,1,2));
        $this->assertSame(null,V::coalesce(null,false,[],0));
    }
    
    /**
     * @covers \Ptilz\V::isType
     */
    function testIsType() {
        $this->assertTrue(V::isType(true,'bool'));
        $this->assertTrue(V::isType(false,'boolean'));
        $this->assertTrue(V::isType(5,'int'));
        $this->assertTrue(V::isType(5,'integer'));
        $this->assertTrue(V::isType(5,'long'));
        $this->assertFalse(V::isType('5','int'));
        $this->assertFalse(V::isType(5.,'int'));
        $this->assertTrue(V::isType(5.,'float'));
        $this->assertTrue(V::isType(5.,'real'));
        $this->assertTrue(V::isType(5.,'double'));
        $this->assertTrue(V::isType(null,'null'));
        $this->assertFalse(V::isType('null','null'));
        $this->assertFalse(V::isType(false,'null'));
        $this->assertTrue(V::isType('foo','string'));
        $this->assertTrue(V::isType('','string'));
        $this->assertTrue(V::isType(new DateTime(),'object'));
        $this->assertTrue(V::isType([1,2],'array'));
        $this->assertTrue(V::isType([],'array'));
        $this->assertTrue(V::isType(new Countable_1454626667(0),\Countable_1454626667::class));
        $this->assertTrue(V::isType(true,'true'));
        $this->assertFalse(V::isType(false,'true'));
        $this->assertFalse(V::isType(true,'false'));
        $this->assertTrue(V::isType(false,'false'));
    }

    /**
     * @covers \Ptilz\V::export
     */
    function testExport() {
        $this->assertSame('3.0',V::export(3.));
        $this->assertSame('"3"',V::export('3'));
        $this->assertSame('true',V::export(true));
        $this->assertSame('null',V::export(null));
        $this->assertSame('[1,"foo",false]',V::export([1,'foo',false]));
        $this->assertSame('["foo"=>"bar","baz"=>null]',V::export(['foo'=>'bar','baz'=>null]));
    }

    /**
     * @test \Ptilz\V::value
     */
    function testValue() {
        $f = function() { return 9; };
        $this->assertSame(9, V::value($f));
        $this->assertSame(9, 9);
    }

    /**
     * @test \Ptilz\V::with
     */
    function testWith() {
        $o = new stdClass();
        $this->assertSame($o, V::with($o));
    }

    /**
     * @test \Ptilz\V::getType
     */
    function testGetType() {
        $this->assertSame('bool',V::getType(true));
        $this->assertSame('int',V::getType(5));
        $this->assertSame('float',V::getType(5.));
        $this->assertSame('array',V::getType([]));
        $this->assertSame(\stdClass::class,V::getType(new stdClass));
    }

    /**
     * @test \Ptilz\V::isOneOfType
     */
    function testIsOneOfType() {
        $this->assertTrue(V::isOneOfType(5,['float','int']));
        $this->assertFalse(V::isOneOfType(5,['float','bool']));
    }

    /**
     * @test \Ptilz\V::assertOneOfType
     */
    function testAssertOneOfType() {
        $this->expectException(\Ptilz\Exceptions\ArgumentTypeException::class);
        V::assertOneOfType(5,['float','bool']);
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