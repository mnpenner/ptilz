<?php
use Ptilz\Bin;
use Ptilz\BitStream;
use Ptilz\Math;
use Ptilz\Str;
use Ptilz\V;

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

    function testSplit() {
        $this->assertSame(['a', 'b'], Str::split('a:b', ':'));
        $this->assertSame(['a', 'b:c'], Str::split('a:b:c', ':', 2));
        $this->assertSame(['a', 'b', 'x', 'x'], Str::split('a:b', ':', 4, 'x'));
        $this->assertSame(['','','',''], Str::split('aaa', 'a'));
    }

    function testRsplit() {
        $this->assertSame(['a', 'b'], Str::rsplit('a:b', ':'));
        $this->assertSame(['a:b', 'c'], Str::rsplit('a:b:c', ':', 2));
        $this->assertSame(['a', 'b', 'x', 'x'], Str::rsplit('a:b', ':', 4, 'x'));
        $this->assertSame(['','','',''], Str::rsplit('aaa', 'a'));
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

        $this->assertSame('a:string 0:int 0.:float []:array stdClass:stdClass null:NULL', Str::format('{0}:{0:t} {1}:{1:t} {2}:{2:t} {3}:{3:t} {4}:{4:t} {5}:{5:t}', 'a', 0, 0., [], new stdClass, null));
        $this->assertSame('#036CF0', Str::formatArgs('#{R:X2}{G:X2}{B:X2}', ['B' => 0xF0, 'G' => 0x6C, 'R' => 0x03]));
        $this->assertSame('0x00f0397c', Str::format('0x{:x8}', "\xF0\x39\x7C"));
        $this->assertSame('01000001:01000010:01000011', Str::format('{:b:}', 'ABC'));
        $this->assertSame('int:2 float:3.', Str::format('int:{:i} float:{:f}', 2.9, 3));
        $this->assertSame('3.14000', Str::format('{:f.5}', 3.14));
        $this->assertSame('00003', Str::format('{:i05}', 3.14));
        $this->assertSame('1,235  1,234.560  1 234,56  1234.56', Str::format('{0:n}  {0:n3}  {0:n2, }  {0:n2.}', 1234.56));
        $this->assertSame('17 410', Str::format('{:o} {:o}', 15, 264), "octal");
        $this->assertSame('A-z', Str::format('{:c}-{:c}', 65, 122));
        $this->assertSame('Hello "world"', Str::format('{} {:S}', 'Hello', 'world'));
        //$this->assertSame('b16,077F', Str::format('{}', "\x07\x7F"));

        $this->assertSame('xyz', Str::format('{:s}', new _toStringable));
        $this->assertSame('3 3.0 "3"', Str::format('{:e} {:e} {:e}', 3, 3., '3'));
        $this->assertSame('65-122', Str::format('{:d}-{:d}', 'A', 'z'));
    }

    function testLength() {
        $this->assertSame(22, Str::length('Cién cañones por banda'));
    }

    function testBinStr() {
        $this->assertSame('00000000', Str::binary("\0"));
        $this->assertSame('01000001 01000010 01000011', Str::binary('ABC'));
    }


    function testAddSlashes() {
        $this->assertSame('A\n\r\t\v\e\f\\\\ \\"\x7F\x7Fz',Str::addSlashes("A\n\r\t\v\x1B\f\\ \"\177\x7Fz"));
    }

    function testInterpretDoubleQuotedString() {
        $this->assertEquals("A\n\r\t\v\x1B\f\\ \"\"\177\x7Fz",Str::interpretDoubleQuotedString('A\n\r\t\v\e\f\\\\ "\\"\177\x7Fz'));
        $this->assertEquals('\Q\\',Str::interpretDoubleQuotedString('\Q\\'),"Q is not an escape sequence, print it literally");
    }

    function testInterpretSingleQuotedString() {
        $this->assertEquals('A\n\r\t\v\e\f\\ "\\"\'\'\177\x7Fz',Str::interpretSingleQuotedString('A\n\r\t\v\e\f\\\\ "\\"\'\\\'\177\x7Fz'));
        $this->assertEquals('\Q\\',Str::interpretSingleQuotedString('\Q\\'));
    }

    /**
     * @depends testAddSlashes
     */
    function testExport() {
        $this->assertSame('"A\n\r\t\v\e\f\\\\ \$\xFFz"', Str::export("A\n\r\t\v\x1B\f\\ \$\xffz"));
        $this->assertSame('"p\x03d"',Str::export("\x70\x03\x64"));
        $this->assertSame('"\x1E\x02"',Str::export("\x1E\x02"));
        $this->assertSame('"\xE1\xB8\x82"',Str::export("\xE1\xB8\x82"));
        $this->assertSame('"\x07"',Str::export("\x07"));
        $this->assertSame('"7"',Str::export("7"));
        $this->assertSame('"\x00\x03"',Str::export("\x00\x03"));
        $this->assertSame('"\x00"',Str::export("\00"));
        $this->assertSame('"\x005"',Str::export(hex2bin('0035')));
    }

    /**
     * @depends testInterpretDoubleQuotedString
     * @depends testInterpretSingleQuotedString
     */
    function testImport() {
        $this->assertSame("A\n\r\t\v\x1B\f\\ \\'\$\xFFz", Str::import('"A\n\r\t\v\e\f\\\\ \\\'\$\xFFz"'));
        $this->assertSame('A\n\r\t\v\e\f\\ \'\$\xFFz', Str::import('\'A\n\r\t\v\e\f\\\\ \\\'\$\xFFz\''));
        $this->assertSame('\Q',Str::import('"\\Q"'));
        $this->assertSame('\Q',Str::import('\'\\Q\''));
        $this->assertSame("\x70\x03\x64",Str::import('"p\x03d"'));
        $this->assertSame('p\x03d',Str::import('\'p\x03d\''));
        $this->assertSame("\x1E\x02", Str::import('"\x1E\x02"'));
        $this->assertSame("\xE1\xB8\x82", Str::import('"\xE1\xB8\x82"'));
        $this->assertSame("\x07",Str::import('"\x07"'));
        $this->assertSame("7",Str::import('"7"'));
        $this->assertSame("\x00\x03",Str::import('"\0\x03"'));
        $this->assertSame("\0",Str::import('"\0"'));
        $this->assertSame("\00",Str::import('"\00"'));
        $this->assertSame("\000",Str::import('"\000"'));
    }
/*
FAIL CASE:
46c78f24f7c359c51d866b916bb80d336d134384a629e8b41207402e3e541e69971998270fd5d5c7fea46740f571112700364fc92c159e9a4e34cd15ef2a8a2e35f7a2fdb593a8b1
46c78f24f7c359c51d866b916bb80d336d134384a629e8b41207402e3e541e69971998270fd5d5c7fea46740f5711127064fc92c159e9a4e34cd15ef2a8a2e35f7a2fdb593a8b1

 */

    /**
     * @depends testExport
     * @depends testImport
     */
    function testExportImport() {
        for($i=0; $i<250; ++$i) {
            $str = Bin::secureRandomBytes(Math::rand(1,250));
            $exp = Str::export($str);
            $imp = Str::import($exp);
            //dump(bin2hex($str));
            //dump($exp);
            //dump($imp);
            $this->assertEquals($str,$imp,$str);
        }
    }

    /**
     * @depends testExport
     */
    function testExportEval() {
        for($i=0; $i<250; ++$i) {
            $str = Bin::secureRandomBytes(Math::rand(1,250));
            $exp = Str::export($str);
            $imp = eval("return $exp;");
            //dump(bin2hex($str));
            //dump($exp);
            //dump($imp);
            $this->assertEquals($str,$imp,"Raw string: $str   Exported string: $exp");
        }
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
        $this->assertSame('Foo123', Str::classify('foo123'));
        $this->assertSame('Foo123', Str::classify('foo 123'));
        $this->assertSame('_123', Str::classify(123,'_'));
        $this->assertSame('i123', Str::classify(123,'i'));
        $this->assertSame('', Str::classify(''));
        $this->assertSame('AddUserTable', Str::classify('Add user table'));
        $this->assertSame('AddUserTable', Str::classify('AddUserTable'));
        $this->assertSame('HtmlSpecialChars', Str::classify('HTMLSpecialChars'));
        $this->assertSame('HtmlDocument', Str::classify('HtmlDocument'));
        $this->assertSame('HtmlDocument', Str::classify('HTML Document'));
        $this->assertSame('NotFoundHttpException', Str::classify('NotFoundHttpException'));
        $this->assertSame('NotFoundHttpException', Str::classify('NotFoundHTTPException'));
        $this->assertSame('NaïveParser', Str::classify('naïve+parser'));
        $this->assertSame('ÉléphantFärt', Str::classify('éléphant|FÄRT'));
        $this->assertSame('LInjectionSql', Str::classify("L'injection SQL"));
        $this->assertSame('MeetTheJonsesDog', Str::classify("Meet the Jonses' Dog"));
        $this->assertSame('MarksKeyboard', Str::classify("Mark's[keyboard]"));
        $this->assertSame('AugustusDagger', Str::classify("Augustus'<DAGGER>"));
        $this->assertSame('Toms', Str::classify("Tom's"));
        $this->assertSame('TomsHardware', Str::classify("Tom's Hardware"));
        $this->assertSame('TomsHardware', Str::classify("Tom’s Hardware"));
        $this->assertSame('TomsHardware', Str::classify("Tom'sHardware"));
        $this->assertSame('TomShardware', Str::classify("tom'shardware"));
        $this->assertSame('Thomas', Str::classify("Thomas'"));
        $this->assertSame('ThomasHardware', Str::classify("Thomas'Hardware"));
        $this->assertSame('ThomasHardware', Str::classify("Thomas' Hardware"));
        $this->assertSame('BaconCheeseDelight', Str::classify("bacon-cheese-delight"));
        $this->assertSame('1sComplement', Str::classify("1's complement"));
        $this->assertSame('TwosComplement', Str::classify("TWO'S COMPLEMENT"));
        $this->assertSame('EfryTpagaNumber', Str::classify("EFRY TPAGA number"));
        $this->assertSame("ChrisMuseum", Str::classify("chris' museum"));
        $this->assertSame("ChrisMuseum", Str::classify("chris’ museum"));
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
        $this->assertSame('add_user_table', Str::slugify('Add user table','_'));
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

    function testSecureRandom() {
        for($i=1; $i<10000; $i *= 3) {
            $str = Str::secureRandom($i, '01');
            $this->assertSame($i, strlen($str));
        }
        for($i=1; $i<10000; $i *= 3) {
            $str = Str::secureRandom($i, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdef');
            $this->assertSame((int)ceil($i/5), strlen($str));
        }
    }

    function testEncodeCharAndLength() {
        for($i=0; $i<50; ++$i) {
            $src_bits = mt_rand(1,256);
            $bin = openssl_random_pseudo_bytes(ceil($src_bits/8));

            $low = mt_rand(0x20,0x7D);
            $high = mt_rand($low+1,0x7E);
            $alpha = implode('',array_map('chr',range($low,$high)));

            $enc = Str::encode(new BitStream($bin,$src_bits), $alpha);

            $outlen = strlen($enc);

            $alpha_bits = log(strlen($alpha),2);

            $min = (int)ceil($src_bits/ceil($alpha_bits));
            $max = (int)ceil($src_bits/floor($alpha_bits));

            $msg = Str::format("Str::encode({:S}:{:S},{:S}) expect between {:n} and {:n}, got {:n}", $bin, $src_bits, $alpha, $min, $max, $outlen);

            $patt = '/['.preg_quote($alpha,'/').']{'.$min.','.$max.'}\z/A';

            $this->assertRegExp($patt,$enc,$msg);

            //$this->assertGreaterThanOrEqual($min,$outlen, $msg);
            //$this->assertLessThanOrEqual($max,$outlen, $msg);
        }
    }

    /**
     * @depends testSecureRandom
     * @depends testRandom
     * @depends testEncodeCharAndLength
     */
    function testEncodeAgainstBase64() {
        $this->assertSame("QQ",Str::encode("A",Str::BASE64),"010000 01");
        $this->assertSame("OG1i",Str::encode("8mb",Str::BASE64));
        $this->assertSame("VGhpcyBpcyBhbiBlbmNvZGVkIHN0cmluZw",Str::encode("This is an encoded string",Str::BASE64));
        $this->assertSame("/w",Str::encode("\xFF",Str::BASE64));

        for($i=0; $i<50; ++$i) {
            $len = mt_rand(1,20);
            $bin = Str::random($len,Str::WORD_CHARACTERS);
            $b64 = base64_encode($bin);
            $enc = Str::encode($bin, Str::BASE64);
            $pad = str_pad($enc,(int)ceil($len/3)*4,'=',STR_PAD_RIGHT);
            $this->assertSame($b64,$pad,Str::format("Base64 encode {:S}", $bin));
        }

        for($i=0; $i<50; ++$i) {
            $len = mt_rand(1,256);
            $bin = openssl_random_pseudo_bytes($len);
            $b64 = base64_encode($bin);
            $enc = Str::encode($bin, Str::BASE64);
            $pad = str_pad($enc,(int)ceil($len/3)*4,'=',STR_PAD_RIGHT);
            $this->assertSame($b64,$pad,Str::format("Base64 encode {:S}", $bin));
        }
    }


    function testBitStreamToString() {
        $this->assertSame('01000001 01000001',(string)new BitStream('AA',16));
        $this->assertSame('01000001 0100000X',(string)new BitStream('AA',15));
        $this->assertSame('0100000X XXXXXXXX',(string)new BitStream('AA',7));
    }

    function testSplitSearchQuery() {
        $this->assertSame(['so','simple'],Str::splitSearchQuery('so simple'),"smoke test");
        $this->assertSame(['plz','halp','so lost','much  confuse'],Str::splitSearchQuery(' plz  halp  "so lost" "much  confuse" '),"strings and spaces");
        $this->assertSame(['文字','化け'],Str::splitSearchQuery('文字 化け'),"unicode");
        $this->assertSame(["o'clock"],Str::splitSearchQuery('"o\'clock"'),"quoted quote");
        $this->assertSame(["o'clock"],Str::splitSearchQuery("o\\'clock"),"escaped quote");
        $this->assertSame(["o","clock"],Str::splitSearchQuery("o'clock'"),"clock is quoted");
        $this->assertSame(["o","clock"],Str::splitSearchQuery("o'clock"),"unterminated string");
        $this->assertSame([],Str::splitSearchQuery(" \r\n\0"),"all whitespace");
        $this->assertSame([],Str::splitSearchQuery('"'),"a dangling quote");
    }

    function testRemoveDiacritics() {
        $this->assertSame("This is a very wrong sentence!",Str::removeDiacritics("Thîs îs à vêry wrong séntènce!")); // http://stackoverflow.com/a/25414406/65387
        $this->assertSame("Je suis alle a l'ecole",Str::removeDiacritics("Je suis allé à l'école"));
        $this->assertSame("Un elephant a l'oree du bois",Str::removeDiacritics('Un éléphant à l\'orée du bois'));
        $this->assertSame("usuario o contrasena incorrectos",Str::removeDiacritics("usuario o contraseña incorrectos")); // http://stackoverflow.com/questions/1017599/how-do-i-remove-accents-from-characters-in-a-php-string#comment43187391_25414406
    }

    function testQuote() {
        $this->assertSame('"foo"',Str::quote('foo'));
        $this->assertSame('""',Str::quote(''));
        $this->assertSame("'bar'",Str::quote('bar',"'"));
        $this->assertSame("«baz»",Str::quote('baz','«','»'));
        $this->assertSame("[[quux]]",Str::quote('quux','[[',']]'));
        $this->assertSame('"foo\\"bar"',Str::quote('foo"bar','"',null,'\\'));
        $this->assertSame('"foo"bar"',Str::quote('foo"bar','"'));
    }

    function testUnquote() {
        $this->assertSame('foo',Str::unquote('"foo"'));
        $this->assertSame('foo',Str::unquote('foo'));
        $this->assertSame('"',Str::unquote('"'));
        $this->assertSame('',Str::unquote(''));
        $this->assertSame('bar',Str::unquote("'bar'","'"));
        $this->assertSame('baz',Str::unquote("«baz»",'«','»'));
        $this->assertSame("«baz»",Str::unquote("«baz»",'«'));
        $this->assertSame('quux',Str::unquote("[[quux]]",'[[',']]'));
        $this->assertSame('[[quux]',Str::unquote("[[quux]",'[[',']]'));
        $this->assertSame('foo"bar',Str::unquote('"foo\\"bar"','"',null,'\\'));
        $this->assertSame('foo\\"bar',Str::unquote('"foo\\"bar"'));
    }

    function testContains() {
        $this->assertTrue(Str::contains('abc','b'));
        $this->assertTrue(Str::contains('abc','bc'));
        $this->assertTrue(Str::contains('abc','abc'));
        $this->assertFalse(Str::contains('abc','abcd'));
        $this->assertFalse(Str::contains('abc','x'));
        $this->assertFalse(Str::contains('abc','B'));
        $this->assertTrue(Str::contains('abc','B',true));
        $this->assertTrue(Str::contains('abc','Ab',true));
        $this->assertTrue(Str::contains('abc','B',true,1));
        $this->assertTrue(Str::contains('abc','BC',true,1));
        $this->assertFalse(Str::contains('abc','BC',true,0));
        $this->assertFalse(Str::contains('abc','BC',false,1));
        $this->assertTrue(Str::contains('abc','bc',false,1));
    }

    function testSmartSplit() {
        $this->assertSame(['a','b','c','d'],Str::smartSplit("a, b,c , d "));
        $this->assertSame(['a',' b','c ',' d '],Str::smartSplit('a," b","c "," d "'));
        $this->assertSame(['a','b,c','d'],Str::smartSplit('a,"b,c",d'));
        $this->assertSame(['a',' b ;; c ','d'],Str::smartSplit("a;;'' b ;; c '';;d",';;',"''"));
        $this->assertSame(['a','b'],Str::smartSplit(",a,, ,b,"));
    }

    /**
     * @dataProvider fileSizeTests
     */
    public function testFileSize($size, $spec, $digits, $decimals, $trim, $expected, $message='') {
        $this->assertEquals($expected, Str::fileSize($size, $spec, $digits, $decimals, $trim), $message);
    }

    public function fileSizeTests() {
        return [
            [999, 'iec', 3, null, false, '999 B'],
            [1000, 'iec', 3, null, false, '1000 B'],
            [1024, 'iec', 3, null, false, '1.00 KiB'],
            [1024**2, 'iec', 3, null, false, '1.00 MiB'],
            [1024**3, 'iec', 3, null, false, '1.00 GiB'],
            [1024**4, 'iec', 3, null, false, '1.00 TiB'],
            [1024**5, 'iec', 3, null, false, '1.00 PiB'],
            [1024**6, 'iec', 3, null, false, '1.00 EiB'],
            [1024**7, 'iec', 3, null, false, '1.00 ZiB'],
            [1024**8, 'iec', 3, null, false, '1.00 YiB'],
            [1024**9, 'iec', 3, null, false, '1024 YiB'],

            [1, 'si', 3, null, false, '1 B'],
            [999, 'si', 3, null, false, '999 B'],
            [1000, 'si', 3, null, false, '1.00 kB'],
            [1024, 'si', 3, null, false, '1.02 kB'],
            [1000**2, 'si', 3, null, false, '1.00 MB'],
            [1000**3, 'si', 3, null, false, '1.00 GB'],
            [1000**4, 'si', 3, null, false, '1.00 TB'],
            [1000**5, 'si', 3, null, false, '1.00 PB'],
            [1000**6, 'si', 3, null, false, '1.00 EB'],
            [1000**7, 'si', 3, null, false, '1.00 ZB'],
            [1000**8, 'si', 3, null, false, '1.00 YB'],
            [1000**9, 'si', 3, null, false, '1000 YB'],

            [1, 'jedec', 2, null, false, '1 bytes'],
            [99, 'jedec', 2, null, false, '99 bytes'],
            [100, 'jedec', 2, null, false, '100 bytes'],
            [972, 'jedec', 2, null, false, '972 bytes'],
            [973, 'jedec', 2, null, false, '973 bytes'],
            [999, 'jedec', 2, null, false, '999 bytes'],
            [1000, 'jedec', 2, null, false, '1000 bytes'],
            [1024, 'jedec', 2, null, false, '1.0 KB'],
            [1075, 'jedec', 2, null, false, '1.0 KB'],
            [1076, 'jedec', 2, null, false, '1.1 KB'],
            [1024**2, 'jedec', 2, null, false, '1.0 MB'],
            [1024**3, 'jedec', 2, null, false, '1.0 GB'],
            [1024**4, 'jedec', 2, null, false, '1.0 TB'],
            [1024**5, 'jedec', 2, null, false, '1.0 PB'],
            [1024**6, 'jedec', 2, null, false, '1.0 EB'],
            [1024**7, 'jedec', 2, null, false, '1.0 ZB'],
            [1024**8, 'jedec', 2, null, false, '1.0 YB'],
            [1024**9, 'jedec', 2, null, false, '1024 YB', "No choice, can't fit inside 2 digits"],
            [10188, 'jedec', 2, null, false, '9.9 KB'],
            [10189, 'jedec', 2, null, false, '10 KB'],


            [1, 'jedec', null, 1, false, '1 bytes'],
            [999, 'jedec', null, 1, false, '999 bytes'],
            [1000, 'jedec', null, 1, false, '1000 bytes'],
            [1023, 'jedec', null, 1, false, '1023 bytes'],
            [1024, 'jedec', null, 1, false, '1.0 KB'],
            [1024**2, 'jedec', null, 1, false, '1.0 MB'],
            [1024**3, 'jedec', null, 1, false, '1.0 GB'],
            [1024**4, 'jedec', null, 1, false, '1.0 TB'],
            [1024**5, 'jedec', null, 1, false, '1.0 PB'],
            [1024**6, 'jedec', null, 1, false, '1.0 EB'],
            [1024**7, 'jedec', null, 1, false, '1.0 ZB'],
            [1024**8, 'jedec', null, 1, false, '1.0 YB'],
            [1024**9, 'jedec', null, 1, false, '1024.0 YB'],
            [10188, 'jedec', null, 1, false, '9.9 KB', "9.94921875 KiB"],
            [10189, 'jedec', null, 1, false, '10.0 KB', "9.9501953125 KiB"],

            [0, 'jedec', 3, null, false, "0 bytes"],
            [1, 'jedec', 3, null, false, "1 bytes"],
            [2, 'jedec', 3, null, false, "2 bytes"],
            [10, 'jedec', 3, null, false, "10 bytes"],
            [99, 'jedec', 3, null, false, "99 bytes"],
            [100, 'jedec', 3, null, false, "100 bytes"],
            [1000, 'jedec', 3, null, false, "1000 bytes"],
            [1023, 'jedec', 3, null, false, "1023 bytes"],
            [1024, 'jedec', 3, null, false, "1.00 KB"],
            [1025, 'jedec', 3, null, false, "1.00 KB"],
            [1029, 'jedec', 3, null, false, "1.00 KB"],
            [1030, 'jedec', 3, null, false, "1.01 KB"],
            [10240, 'jedec', 3, null, false, "10.0 KB"],
            [10239, 'jedec', 3, null, false, "10.0 KB"],
            [10241, 'jedec', 3, null, false, "10.0 KB"],
            [10229, 'jedec', 3, null, false, "9.99 KB"],
            [10230, 'jedec', 3, null, false, "9.99 KB"],
            [10234, 'jedec', 3, null, false, "9.99 KB"],
            [10235, 'jedec', 3, null, false, "10.0 KB"],
            [102400, 'jedec', 3, null, false, "100 KB"],
            [1000000, 'jedec', 3, null, false, "977 KB", "off by 448 B (0.045%)"], // Windows actually shows 976 KB! http://i.imgur.com/uHMof8f.png  i.e. Windows truncates
            [1023999, 'jedec', 3, null, false, "999 KB", "off by 1023 B (0.10%)"], // 999 KB is off by 1023 B whereas 0.98 MB is off by 3605.48 bytes
            [1024000, 'jedec', 3, null, false, "999 KB", "off by 1024 B (0.10%)"], // even though 1024000 is exactly 1000 KB, we're introducing a deliberate rounding error to fit the 3 digit limit
            [1025290, 'jedec', 3, null, false, "999 KB", "off by 2314 B (0.23%)"], // 999 KB = 1,022,976 B (error = 2314 B); 0.98 MB = 1027604.48 B (2314.48 B) -- http://i.imgur.com/Cr1ZZKF.png
            // 0.97 MB should never show because that's 993.28 KB, still within our 3 digit limit and 1000 KB is closer to 0.98 MB than 0.97 MB but actually closest to 999 KB
            [1025291, 'jedec', 3, null, false, "0.98 MB", "off by 2313.48 B (0.23%)"], // 999 KB = 1,022,976 B (error = 2315 B); 0.98 MB = 1027604.48 B (2313.48 B)
            [1027604, 'jedec', 3, null, false, "0.98 MB", "off by 0.48 B (~0%)"], // 999 KB = 1,022,976 B (error = 4628 B); 0.98 MB = 1027604.48 B (0.48 B) -- http://i.imgur.com/HlY3UsE.png
            [1027605, 'jedec', 3, null, false, "0.98 MB", "off by 0.52 B (~0%)"], // Microsoft *still* rounds down to 0.97 even though 1027605 B is strictly greater than 0.98 MB http://i.imgur.com/vorddWc.png

            [1, 'jedec', null, 2, true, '1 bytes'],
            [999, 'jedec', null, 2, true, '999 bytes'],
            [1000, 'jedec', null, 2, true, '1000 bytes'],
            [1024, 'jedec', null, 2, true, '1 KB'],
            [1024**2, 'jedec', null, 2, true, '1 MB'],
            [1024**3, 'jedec', null, 2, true, '1 GB'],
            [1024**4, 'jedec', null, 2, true, '1 TB'],
            [1024**5, 'jedec', null, 2, true, '1 PB'],
            [1024**6, 'jedec', null, 2, true, '1 EB'],
            [1024**7, 'jedec', null, 2, true, '1 ZB'],
            [1024**8, 'jedec', null, 2, true, '1 YB'],
            [1024**9, 'jedec', null, 2, true, '1024 YB'],
            [10188, 'jedec', null, 2, true, '9.95 KB'],
            [10189, 'jedec', null, 2, true, '9.95 KB'],
            [10491002, 'jedec', null, 2, true, '10 MB'],
            [10491003, 'jedec', null, 2, true, '10.01 MB'],
            [10591000, 'jedec', null, 2, true, '10.1 MB'],
            [10591000, 'jedec', null, 2, true, '10.1 MB'],
            [10585374, 'jedec', null, 2, true, '10.09 MB'],
            [10585375, 'jedec', null, 2, true, '10.1 MB'],

            [-1, 'jedec', null, 2, true, '-1 bytes'],
            [-999, 'jedec', null, 2, true, '-999 bytes'],
            [-1000, 'jedec', null, 2, true, '-1000 bytes'],
            [-1024, 'jedec', null, 2, true, '-1 KB'],
            [-1024**2, 'jedec', null, 2, true, '-1 MB'],
            [-1024**3, 'jedec', null, 2, true, '-1 GB'],
            [-1024**4, 'jedec', null, 2, true, '-1 TB'],
            [-1024**5, 'jedec', null, 2, true, '-1 PB'],
            [-1024**6, 'jedec', null, 2, true, '-1 EB'],
            [-1024**7, 'jedec', null, 2, true, '-1 ZB'],
            [-1024**8, 'jedec', null, 2, true, '-1 YB'],
            [-1024**9, 'jedec', null, 2, true, '-1024 YB'],
            [-10188, 'jedec', null, 2, true, '-9.95 KB'],
            [-10189, 'jedec', null, 2, true, '-9.95 KB'],
            [-10491002, 'jedec', null, 2, true, '-10 MB'],
            [-10491003, 'jedec', null, 2, true, '-10.01 MB'],
            [-10591000, 'jedec', null, 2, true, '-10.1 MB'],
            [-10591000, 'jedec', null, 2, true, '-10.1 MB'],
            [-10585374, 'jedec', null, 2, true, '-10.09 MB'],
            [-10585375, 'jedec', null, 2, true, '-10.1 MB'],

            [1048575, 'jedec', null, null, false, '1023.9990234375 KB'],
            [1048575, 'jedec', null, null, true, '1023.9990234375 KB'],
            [1048575, 'jedec', null, 10, false, '1023.9990234375 KB'],
            [1048575, 'jedec', null, 15, false, '1023.999023437500000 KB'],
            [1048575, 'jedec', null, 15, true, '1023.9990234375 KB'],

            [999999, 'si', null, null, false, '999.999 kB'],
            [999999, 'si', null, null, true, '999.999 kB'],
            [999999, 'si', null, 2, false, '1.00 MB'],
            [999999, 'si', 3, null, false, '1.00 MB'],
            [999999, 'si', null, 3, false, '999.999 kB'],
            [999999, 'si', null, 4, false, '999.9990 kB'],
            [999999, 'si', null, 4, true, '999.999 kB'],

            [999, 'iec', 3, null, false, '999 B'],
            [1000, 'iec', 3, null, false, '1000 B'],
            [1012, 'iec', 3, null, false, '1012 B'],
            [1018, 'iec', 3, null, false, '1018 B'],
            [1019, 'iec', 3, null, false, '1019 B'],
            [1024, 'iec', 3, null, false, '1.00 KiB'],
        ];
    }

    /**
     * @param $input
     * @param $pad_length
     * @param $pad_string
     * @param $pad_type
     * @param $encoding
     * @param $expected
     * @throws \Ptilz\Exceptions\NotImplementedException
     * @dataProvider testPadArgs
     */
    function testPad($input, $pad_length, $pad_string, $pad_type, $encoding, $expected) {
        $this->assertSame($expected, Str::pad($input, $pad_length, $pad_string, $pad_type, $encoding));
    }

    function testPadArgs() {
        return [
            ['a',3,'b',STR_PAD_LEFT,'utf8','bba'],
            ['a',3,'b',STR_PAD_RIGHT,'utf8','abb'],
            ['',3,'b',STR_PAD_RIGHT,'utf8','bbb'],
            ['a',5,'bcd',STR_PAD_LEFT,'utf8','bcdba'],
            ['a',5,'bcd',STR_PAD_RIGHT,'utf8','adbcd'],
            ['✓',3,'xx',STR_PAD_LEFT,'utf8','xx✓'],
            ['✓',3,'xx',STR_PAD_RIGHT,'utf8','✓xx'],
            ['x',3,'✓',STR_PAD_LEFT,'utf8','✓✓x'],
            ['x',3,'✓',STR_PAD_RIGHT,'utf8','x✓✓'],
            ['☺☻',5,'♡♥',STR_PAD_LEFT,'utf8','♡♥♡☺☻'],
            ['☺☻',5,'♡♥',STR_PAD_RIGHT,'utf8','☺☻♥♡♥'],
            ['╪',4,'═╡',STR_PAD_BOTH,'utf8','═╪═╡'],
            ['a',-3,'b',STR_PAD_LEFT,'utf8','a'],
            ['a',3,'b',STR_PAD_LEFT,'8bit','bba'],
            ['a',3,'b',STR_PAD_LEFT,'ascii','bba'],
            ['^',5,'_.-',STR_PAD_BOTH,'8bit','_.^.-'],
        ];
    }

    function testSplitQuery() {
        $this->assertSame(['so','simple'],Str::splitSearchQuery('so simple'),"smoke test");
        $this->assertSame(['plz','halp','so lost','much  confuse'],Str::splitSearchQuery(' plz  halp  "so lost" "much  confuse" '),"strings and spaces");
        $this->assertSame(['文字','化け'],Str::splitSearchQuery('文字 化け'),"unicode");
        $this->assertSame(["o'clock"],Str::splitSearchQuery('"o\'clock"'),"quoted quote");
        $this->assertSame(["o'clock"],Str::splitSearchQuery("o\\'clock"),"escaped quote");
        $this->assertSame(["o","clock"],Str::splitSearchQuery("o'clock'"),"clock is quoted");
        $this->assertSame(["o","clock"],Str::splitSearchQuery("o'clock"),"unterminated string"); // FIXME: I think this should return a single term
        $this->assertSame([],Str::splitSearchQuery(" \r\n\0"),"all whitespace");
        $this->assertSame([],Str::splitSearchQuery('"'),"a dangling quote");
    }
}

class _toStringable {
    function __toString() {
        return 'xyz';
    }

}