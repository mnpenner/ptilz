<?php
use Ptilz\BigMath;
use Ptilz\Math;

class MathTest extends PHPUnit_Framework_TestCase {
    function testClamp() {
        $this->assertEquals(5, Math::clamp(4, 5, 10));
        $this->assertEquals(10, Math::clamp(11, 5, 10));
        $this->assertEquals(10, Math::clamp(11, 5, 10.0));
        $this->assertEquals(0, Math::clamp(0, -3, 2));
    }

    function testHexToDec() {
        $this->assertSame(15, Math::hexToDec('f'));
        $this->assertSame(15, Math::hexToDec('F'));
        $this->assertSame(16, Math::hexToDec('10'));
        $this->assertEquals('9223372036854775807', Math::hexToDec('7fffffffffffffff'));
        $this->assertEquals('9223372036854775808', Math::hexToDec('8000000000000000'));
    }

    function testDecToHex() {
        $this->assertSame('a', Math::decToHex(10));
        $this->assertSame('A', Math::decToHex(10, true));
        $this->assertEquals('7fffffffffffffff', Math::decToHex('9223372036854775807'));
        $this->assertEquals('8000000000000000', Math::decToHex('9223372036854775808'));
    }

    function testAnyDecToBase() {
        $this->assertSame('aZl8N0y58M7', Math::decToAnyBase('9223372036854775807', 62));
        $this->assertSame('aZl8N0y58M8', Math::decToAnyBase('9223372036854775808', 62));
    }

    function testAnyBaseToDec() {
        $this->assertEquals('9223372036854775807', Math::anyBaseToDec('aZl8N0y58M7', 62));
        $this->assertEquals('9223372036854775808', Math::anyBaseToDec('aZl8N0y58M8', 62));
    }

    function testBetween() {
        $this->assertTrue(BigMath::between(0, PHP_INT_MIN, PHP_INT_MAX));
        $this->assertTrue(BigMath::between(0, 0, 1));
        $this->assertFalse(BigMath::between(0, 0, 1, false));
        $this->assertFalse(BigMath::between('9223372036854775808', PHP_INT_MIN, PHP_INT_MAX));
    }

    function testChangeBase() {
        $this->assertSame('3E8', Math::changeBase('1750', 8, 16, '0123456789ABCDEF'));
    }

    function testToInt() {
        $this->assertInternalType('int', BigMath::toInt(PHP_INT_MIN));
        $this->assertInternalType('int', BigMath::toInt(0));
        $this->assertSame(PHP_INT_MAX, BigMath::toInt(PHP_INT_MAX));
        $this->assertInternalType('string', BigMath::toInt(bcadd(PHP_INT_MAX, 1)));
    }

    function testAdd() {
        $this->assertSame(3, BigMath::add(1, 2));
        $this->assertSame(3, BigMath::add('1', '2'));
        $this->assertSame(-1, BigMath::add(1, '-2'));
        $this->assertEquals('9223372036854775808', BigMath::add('9223372036854775807', '1'));
    }

    function testMul() {
        $this->assertSame(6, BigMath::mul('2', '3'));
        $this->assertEquals('9223372037000250000', BigMath::mul('3037000500', '3037000500'));
    }

    function testMean() {
        $this->assertEquals(2.5,Math::mean([1,2,3,4]));
        $this->assertEquals(4.5,Math::mean([1,4,2,8,7,6,3,5]));
        $this->assertEquals(46,Math::mean([100,0,41,43]));
    }

    function testTruncatedMean() {
        $this->assertEquals(2.5,Math::truncatedMean([0,2,3,400],.25));
        $this->assertEquals(2.5,Math::truncatedMean([0,2,3,400],.4999));
        $this->assertEquals(4.5,Math::truncatedMean([-100,4,2,800,7,6,3,5],.125));
        $this->assertEquals(42,Math::truncatedMean([100,0,41,43],.25));
    }
}