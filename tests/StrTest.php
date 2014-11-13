<?php
use Ptilz\Str;

class StrTest extends PHPUnit_Framework_TestCase {

    function testIsBlank() {
        $this->assertTrue(Str::isBlank(''));
        $this->assertTrue(Str::isBlank(null));
        $this->assertTrue(Str::isBlank('  '));
        $this->assertTrue(Str::isBlank("\t"));
        $this->assertFalse(Str::isBlank('a'));
        $this->assertFalse(Str::isBlank('0'));
    }

    function testIsEmpty() {
        $this->assertTrue(Str::isEmpty(''));
        $this->assertTrue(Str::isEmpty(null));
        $this->assertFalse(Str::isEmpty('  '));
        $this->assertFalse(Str::isEmpty("\t"));
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
        $this->assertSame('xyqzz dog', Str::replace(['cat' => 'dog', 'a' => 'x', 'b' => 'y', 'c' => 'z'], 'abqcc cat'));
        $this->assertSame('xyqzz zxt', Str::replace(['a' => 'x', 'b' => 'y', 'c' => 'z', 'cat' => 'dog'], 'abqcc cat'));
    }

    function testFormat() {
        $this->assertSame('abc', Str::format('abc'));
        $this->assertSame('abcd', Str::format('a{}c{}', 'b', 'd'));
        $this->assertSame('abcb', Str::format('a{0}c{0}', 'b'));
        $this->assertSame('adcb', Str::format('a{1}c{0}', 'b', 'd'));
        $this->assertSame('2 1 1 2', Str::format('{1} {} {0} {}', 1, 2));

        $this->assertSame('a:string 0:integer 0.0:double []:array stdClass:stdClass null:NULL', Str::format('{0}:{0:T} {1}:{1:T} {2}:{2:T} {3}:{3:T} {4}:{4:T} {5}:{5:T}', 'a', 0, 0., [], new stdClass, null));
        $this->assertSame('#036CF0', Str::formatArgs('#{R:X2}{G:X2}{B:X2}', ['B' => 0xF0, 'G' => 0x6C, 'R' => 0x03]));
        $this->assertSame('0x00f0397c', Str::format('0x{:x8}', "\xF0\x39\x7C"));
        $this->assertSame('01000001:01000010:01000011', Str::format('{:b:}', 'ABC'));
        $this->assertSame('int:2 float:3.0', Str::format('int:{:i} float:{:f}', 2.9, 3));
        $this->assertSame('3.14000', Str::format('{:f.5}', 3.14));
        $this->assertSame('00003', Str::format('{:i05}', 3.14));
        $this->assertSame('1,235  1,234.560  1 234,56  1234.56', Str::format('{0:n}  {0:n3}  {0:n2, }  {0:n2.}', 1234.56));
        $this->assertSame('17 410', Str::format('{:o} {:o}', 15, 264));
        $this->assertSame('Az', Str::format('{:c}{:c}', 65, 122));
        $this->assertSame('Hello "world"', Str::format('{} {:V}', 'Hello', 'world'));
        $this->assertSame('b16,077F', Str::format('{}', "\x07\x7F"));
    }

    function testLength() {
        $this->assertSame(22, Str::length('Cién cañones por banda'));
    }

    function testBinStr() {
        $this->assertSame('00000000', Str::binary("\0"));
        $this->assertSame('01000001 01000010 01000011', Str::binary('ABC'));
    }

    function testExport() {
        $this->assertSame('"A\n\r\t\v\e\f\\\\ \$\xFFz"', Str::export("A\n\r\t\v\x1B\f\\ \$\xffz"));
    }

    function testIsBinary() {
        $this->assertFalse(Str::isBinary("The quick brown fox jumped over the lazy dog.\n"));
        $this->assertTrue(Str::isBinary("msword\nwacz\0"));
    }

    function testClassify() {
        $this->assertSame('SomeClassName', Str::classify("some_class_name"));
    }

    function testUnderscored() {
        $this->assertSame('moz_transform', Str::underscored("MozTransform"));
    }

    function testDasherize() {
        $this->assertSame('-moz-transform', Str::dasherized("MozTransform"));
    }

    function testHumanize() {
        $this->assertSame('Capitalize dash camel case underscore trim', Str::humanize('  capitalize dash-CamelCase_underscore trim  '));
    }

    function testSlugify() {
        $this->assertSame('un-elephant-a-loree-du-bois', Str::slugify("Un éléphant à l'orée du bois"));
    }

    function testTruncateWords() {
        $this->assertSame('Hello...', Str::truncateWords('Hello, world',5));
        $this->assertSame('Hello...', Str::truncateWords('Hello, world',8));
        $this->assertSame('Hello, world', Str::truncateWords('Hello, world',5,' (read a lot more)'),'Adding "(read a lot more)" would be longer than the original string');
        $this->assertSame('Hello, cruel...', Str::truncateWords('Hello, cruel world',15));
        $this->assertSame('Hello', Str::truncateWords('Hello',10));
    }
}