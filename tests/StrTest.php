<?php
use Ptilz\Str;
mb_internal_encoding('UTF-8');

class StrTest extends PHPUnit_Framework_TestCase {
    function testStartsWith() {
        $this->assertTrue(Str::startsWith("abc","a"));
        $this->assertFalse(Str::startsWith("abc","A"));
        $this->assertTrue(Str::startsWith("abc","A",true));
        $this->assertTrue(Str::startsWith("Åland","åla",true));
        $this->assertFalse(Str::startsWith("Åland","Ala"));
    }

    function testEndsWith() {
        $this->assertTrue(Str::endsWith("abc","c"));
        $this->assertFalse(Str::endsWith("abc","C"));
        $this->assertTrue(Str::endsWith("abc","C",true));
        $this->assertTrue(Str::endsWith("résumé","UMÉ",true));
        $this->assertFalse(Str::endsWith("exposé","ose"));
    }

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
        $this->assertSame('MyWonderfullClassName', Str::classify("my wonderfull class_name"));
        $this->assertSame('MyWonderfullClassName', Str::classify("my wonderfull.class.name"));
        $this->assertSame('MyLittleCamel', Str::classify("myLittleCamel"));
        $this->assertSame('MyLittleCamelClassName', Str::classify("myLittleCamel.class.name"));
        $this->assertSame('123', Str::classify(123));
        $this->assertSame('', Str::classify(''));
    }

    function testUnderscored() {
        $this->assertSame('moz_transform', Str::underscored("MozTransform"));
        $this->assertSame('the_underscored_string_method', Str::underscored("the-underscored-string-method"));
        $this->assertSame('the_underscored_string_method', Str::underscored("theUnderscoredStringMethod"));
        $this->assertSame('the_underscored_string_method', Str::underscored("TheUnderscoredStringMethod"));
        $this->assertSame('the_underscored_string_method', Str::underscored(" the underscored  string method"));
        $this->assertSame('html_parser', Str::underscored("HtmlParser"));
        $this->assertSame('html_parser', Str::underscored("HTMLParser"));
        $this->assertSame('', Str::underscored(""));
    }

    function testDasherize() {
        $this->assertSame('-moz-transform', Str::dasherized("MozTransform"));
        $this->assertSame('the-dasherize-string-method', Str::dasherized("the_dasherize_string_method"));
        $this->assertSame('-the-dasherize-string-method', Str::dasherized("TheDasherizeStringMethod"));
        $this->assertSame('this-is-a-test', Str::dasherized("thisIsATest"));
        $this->assertSame('this-is-a-test', Str::dasherized("this Is A Test"));
        $this->assertSame('this-is-a-test123', Str::dasherized("thisIsATest123"));
        $this->assertSame('123this-is-a-test', Str::dasherized("123thisIsATest"));
        $this->assertSame('123this-is-a-test', Str::dasherized("123thisIsATest"));
        $this->assertSame('the-dasherize-string-method', Str::dasherized("the dasherize string method"));
        $this->assertSame('the-dasherize-string-method', Str::dasherized("the  dasherize string method  "));
        $this->assertSame('téléphone', Str::dasherized("téléphone"));
        $this->assertSame('foo-bar', Str::dasherized('foo$bar')); // differs from underscore.string https://github.com/epeli/underscore.string/blob/4774b3e9a22d05073261d9872f9d11ad5de8ee5b/test/strings.js#L443
        $this->assertSame('-html-parser', Str::dasherized("HtmlParser"));
        $this->assertSame('-html-parser', Str::dasherized("HTMLParser"));
        $this->assertSame('html-parser', Str::dasherized("htmlParser"));
        $this->assertSame('', Str::dasherized(''));
    }

    function testHumanize() {
        $this->assertSame('Capitalize dash camel case underscore trim', Str::humanize('  capitalize dash-CamelCase_underscore trim  '));
        $this->assertSame('This is a test', Str::humanize('thisIsATest'));
        $this->assertSame('The humanize string method', Str::humanize('the_humanize_string_method'));
        $this->assertSame('Thehumanize string method', Str::humanize('ThehumanizeStringMethod'));
        $this->assertSame('Thehumanize string method', Str::humanize('-ThehumanizeStringMethod'));
        $this->assertSame('The humanize string method', Str::humanize('the humanize string method'));
        $this->assertSame('The humanize id string method', Str::humanize('the humanize_id string method_id'));
        $this->assertSame('The humanize string method', Str::humanize('the  humanize string method  '));
        $this->assertSame('Html parser', Str::humanize("HtmlParser"));
        $this->assertSame('Html parser', Str::humanize("HTMLParser"));
    }

    function testSlugify() {
        $this->assertSame('jack-jill-like-numbers-1-2-3-and-4-and-silly-characters', Str::slugify('Jack & Jill like numbers 1,2,3 and 4 and silly characters ?%.$!/'));
        $this->assertSame('un-elephant-a-loree-du-bois', Str::slugify('Un éléphant à l\'orée du bois'));
        $this->assertSame('i-know-latin-characters-a-i-o-u-c-a-o-n-u-a-s-t', Str::slugify('I know latin characters: á í ó ú ç ã õ ñ ü ă ș ț'));
        $this->assertSame('i-am-a-word-too-even-though-i-am-but-a-single-letter-i', Str::slugify('I am a word too, even though I am but a single letter: i!'));
        $this->assertSame('', Str::slugify(''));
    }

    function testTruncate() {
        $this->assertSame('Hello...', Str::truncate('Hello world',6,'...',false));
        $this->assertSame('Hello world', Str::truncate('Hello world',6,'read more',false));
        $this->assertSame('Hello read more', Str::truncate('Hello, cruel world',6,' read more',true));
        $this->assertSame('Hello, world', Str::truncate('Hello, world',5,'read a lot more',true));
        $this->assertSame('Hello...', Str::truncate('Hello, world',5,'...',true));
        $this->assertSame('Hello, cruel...', Str::truncate('Hello, cruel world',15,'...',true));
        $this->assertSame('Hello world', Str::truncate('Hello world',22,'...',true));
        $this->assertSame('Un éléphant à...', Str::truncate('Un éléphant à l\'orée du bois',13,'...',true));
        $this->assertSame('Привет read more', Str::truncate('Привет, жестокий мир', 6,' read more',true));
        $this->assertSame('Привет, мир', Str::truncate('Привет, мир', 6,' read a lot more',true));
        $this->assertSame('Привет...', Str::truncate('Привет, мир', 6,'...',true));
        $this->assertSame('Привет...', Str::truncate('Привет, мир', 8,'...',true));
        $this->assertSame('Привет, жестокий...', Str::truncate('Привет, жестокий мир', 16,'...',true));
        $this->assertSame('Привет, мир', Str::truncate('Привет, мир', 22,'...',true));
        $this->assertSame('alksjd!!!!!!....', Str::truncate('alksjd!!!!!!....', 100,'',true));
    }

    function testReverse() {
        $this->assertSame('cba',Str::reverse('abc'));
        $this->assertSame('sevlaçnoG',Str::reverse('Gonçalves'));
    }

    function testCollapseWhitespace() {
        $this->assertSame("",Str::collapseWhitespace(""));
        $this->assertSame("x",Str::collapseWhitespace("x"));
        $this->assertSame("x",Str::collapseWhitespace("x "));
        $this->assertSame("x",Str::collapseWhitespace(" x"));
        $this->assertSame("a b",Str::collapseWhitespace(" a b "));
        $this->assertSame("a b c de f g",Str::collapseWhitespace("    a     b   c de \n f\n\r  \t g  "));
        $this->assertSame("a b",Str::collapseWhitespace(" \t\n\r\0\x0Ba \t\n\r\0\x0Bb \t\n\r\0\x0B"));
    }

    function testMbReplace() {
        $this->assertSame('bbb',Str::mbReplace('a','b','aaa','auto',$count1));
        $this->assertSame(3,$count1);
        $this->assertSame('ccc',Str::mbReplace(['a','b'],['b','c'],'aaa','auto',$count2));
        $this->assertSame(6,$count2);
        $this->assertSame("\xbf\x5c\x27",Str::mbReplace("\x27","\x5c\x27","\xbf\x27",'iso-8859-1'));
        $this->assertSame("\xbf\x27",Str::mbReplace("\x27","\x5c\x27","\xbf\x27",'gbk'));
    }

    function testJoin() {
        $arr = ['a', 'bc', 'd'];
        $this->assertSame('abcd',Str::join($arr));
        $this->assertSame('a,bc,d',Str::join($arr,','));

        $arrIt = new ArrayIterator($arr);
        $this->assertSame('abcd',Str::join($arrIt));
        $this->assertSame('a,bc,d',Str::join($arrIt,','));

        $f = function() {
            yield 'a';
            yield 'bc';
            yield 'd';
        };
        $this->assertSame('abcd',Str::join($f()));
        $this->assertSame('a | bc | d',Str::join($f(),' | '));


    }
}