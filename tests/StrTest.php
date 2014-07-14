<?php
use Ptilz\Str;

class StrTest extends PHPUnit_Framework_TestCase {

    function testIsEmpty() {
        $this->assertTrue(Str::isEmpty(''));
        $this->assertTrue(Str::isEmpty(null));
        $this->assertTrue(Str::isEmpty('  '));
        $this->assertTrue(Str::isEmpty("\t"));
        $this->assertFalse(Str::isEmpty('a'));
        $this->assertFalse(Str::isEmpty('0'));
    }

    function testIsInt() {
        $this->assertTrue(Str::isInt('0'), "An edge case");
        $this->assertTrue(Str::isInt(' 1 '), "Whitespace");
        $this->assertTrue(Str::isInt(' -1 '), "Negative number + whitespace");
        $this->assertTrue(Str::isInt('36893488147419103232'), "> PHP_INT_MAX");
        $this->assertTrue(Str::isInt(PHP_INT_MAX), "An actual integer");
        $this->assertFalse(Str::isInt('- 1'), "A bad space");
        $this->assertFalse(Str::isInt('a'), "NaN");
        $this->assertFalse(Str::isInt('1e2'), "Scientific notation");
        $this->assertFalse(Str::isInt('0xFF'), "Hex");
        $this->assertFalse(Str::isInt(' 1 ', false), "Disallowed whitespace");
        $this->assertFalse(Str::isInt('-1', false, false), "Disallowed negative number");
    }

    function testRandom() {
        $this->assertRegExp('~[abc123]{30}\z~A', Str::random(30, 'abc123'));
    }

    function testRsplit() {
        $this->assertSame(['a', 'b'], Str::rsplit('a:b', ':'));
        $this->assertSame(['a:b', 'c'], Str::rsplit('a:b:c', ':', 2));
        $this->assertSame(['a', 'b', 'x', 'x'], Str::rsplit('a:b', ':', 4, 'x'));
    }

    function testReplaceAssoc() {
        $this->assertSame('xyqzz dog', Str::replaceAssoc(['cat' => 'dog', 'a' => 'x', 'b' => 'y', 'c' => 'z'], 'abqcc cat'));
        $this->assertSame('xyqzz zxt', Str::replaceAssoc(['a' => 'x', 'b' => 'y', 'c' => 'z', 'cat' => 'dog'], 'abqcc cat'));
    }
}