<?php
use Ptilz\Math;

class MathTest extends PHPUnit_Framework_TestCase {
    function testClamp() {
        $this->assertEquals(5, Math::clamp(4, 5, 10));
        $this->assertEquals(10, Math::clamp(11, 5, 10));
        $this->assertEquals(10, Math::clamp(11, 5, 10.0));
        $this->assertEquals(0, Math::clamp(0, -3, 2));
    }

    function testHexdec() {
        $this->assertSame(15, Math::hexToDec('f'));
        $this->assertSame(15, Math::hexToDec('F'));
        $this->assertSame(16, Math::hexToDec('10'));
        $this->assertSame(9223372036854775807, Math::hexToDec('7fffffffffffffff'));
        $this->assertSame('9223372036854775808', Math::hexToDec('8000000000000000'));
    }

    function testDechex() {
        $this->assertSame('a', Math::decToHex(10));
        $this->assertSame('A', Math::decToHex(10, true));
        $this->assertSame('7fffffffffffffff', Math::decToHex(9223372036854775807));
        $this->assertSame('8000000000000000', Math::decToHex('9223372036854775808'));
    }

    function testDec2base() {
        $this->assertSame('aZl8N0y58M7', Math::decToAnyBase(9223372036854775807, 62));
        $this->assertSame('aZl8N0y58M8', Math::decToAnyBase('9223372036854775808', 62));
    }

    function testBase2dec() {
        $this->assertSame(9223372036854775807, Math::anyBaseToDec('aZl8N0y58M7', 62));
        $this->assertSame('9223372036854775808', Math::anyBaseToDec('aZl8N0y58M8', 62));
    }

    function testBetween() {
        $this->assertTrue(Math::between(0, PHP_INT_MIN, PHP_INT_MAX));
        $this->assertTrue(Math::between(0, 0, 1));
        $this->assertFalse(Math::between(0, 0, 1, false));
        $this->assertFalse(Math::between('9223372036854775808', PHP_INT_MIN, PHP_INT_MAX));
    }

    function testChangeBase() {
        $this->assertSame('3E8', Math::changeBase('1750', 8, 16, '0123456789ABCDEF'));
    }
}