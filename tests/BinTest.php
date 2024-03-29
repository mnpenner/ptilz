<?php
use Ptilz\Bin;

class BinTest extends PHPUnit_Framework_TestCase {

    function testEndian() {
        $this->assertInternalType('bool', Bin::isLittleEndian());
        $this->assertInternalType('bool', Bin::isBigEndian());
        $this->assertTrue(Bin::isLittleEndian() !== Bin::isBigEndian());
    }

    function testUnpack() {
        $this->assertEquals(1<<8, Bin::unpack('-uint16', "\x00\x01"));
        $this->assertEquals(1<<8, Bin::unpack('+uint16', "\x01\x00"));
        $this->assertEquals(1<<24, Bin::unpack('-uint32', "\x00\x00\x00\x01"));
        $this->assertEquals(1<<24, Bin::unpack('+uint32', "\x01\x00\x00\x00"));

        if(Bin::isLittleEndian()) {
            $this->assertEquals(1<<8, Bin::unpack('uint16', "\x00\x01"));
        } else {
            $this->assertEquals(1<<8, Bin::unpack('uint16', "\x01\x00"));
        }

        $this->assertEquals('HELO', Bin::unpack('str[4]', "HELO"));
        if(PHP_INT_SIZE >= 8) {
            $this->assertEquals(['11170778902352744348', '11170778902352744348'], Bin::unpack(['-uint64', '+uint64'], "\x9C\xCF\x29\xF3\x39\x94\x06\x9B\x9B\x06\x94\x39\xF3\x29\xCF\x9C"));
        }

        $this->assertEquals([1,2,3], Bin::unpack('byte{3}', "\x01\x02\x03"));
        $this->assertEquals([
            'strings' => ['aa', 'bb', 'cc'],
            'ints' => [1, 2, 3],
        ], Bin::unpack(['strings' => 'str[2]{3}', 'ints' => '-uint16{3}'], "aabbcc\x01\x00\x02\x00\x03\x00"));

        $this->assertSame(0xABC, Bin::unpack('-uint64', "\xBC\x0A\x00\x00\x00\x00\x00\x00"));
        $this->assertSame(0xABC, Bin::unpack('+uint64', "\x00\x00\x00\x00\x00\x00\x0A\xBC"));
        $this->assertEquals(['3199925962', '3735928559'], Bin::unpack(['-uint32', '+uint32'], "\xCA\xFE\xBA\xBE\xDE\xAD\xBE\xEF"));
        $this->assertSame(0xABC, Bin::unpack('-uint48', "\xBC\x0A\x00\x00\x00\x00"));
        $this->assertSame(0xABC, Bin::unpack('+uint48', "\x00\x00\x00\x00\x0A\xBC"));


        $this->assertEquals(9.183409485952689E-41, Bin::unpack('-float', hex2bin('ffff0000')),'',1.e-100);
        $this->assertEquals(9.183409485952689E-41, Bin::unpack('-float32', hex2bin('ffff0000')),'',1.e-100);

        $this->assertEquals(9.183409485952689E-41, Bin::unpack('+float', hex2bin('0000ffff')),'',1.e-100);
        $this->assertEquals(9.183409485952689E-41, Bin::unpack('+float32', hex2bin('0000ffff')),'',1.e-100);

        $this->assertEquals(2.1219957905E-314, Bin::unpack('-float64', hex2bin('ffffffff00000000')),'',1.e-100);
        $this->assertEquals(2.1219957905E-314, Bin::unpack('-double', hex2bin('ffffffff00000000')),'',1.e-100);

        $this->assertEquals(2.1219957905E-314, Bin::unpack('+float64', hex2bin('00000000ffffffff')),'',1.e-100);
        $this->assertEquals(2.1219957905E-314, Bin::unpack('+double', hex2bin('00000000ffffffff')),'',1.e-100);
    }

    function testUnpackZip() {
        $fileContents = file_get_contents(__DIR__ . '/unpack.zip');
        $offset = 0;

        $pkZipFormat = [
            'signature' => 'str[4]',
            'version' => '-uint16',
            'flags' => '-uint16',
            'compression' => '-uint16',
            'modtime' => '-uint16',
            'moddate' => '-uint16',
            'crc32' => '-uint32',
            'compressed_size' => '-uint32',
            'uncompressed_size' => '-uint32',
            'filename_len' => '-uint16',
            'extra_field_len' => '-uint16',
            'filename' => 'str[filename_len]',
            'extra_field' => 'str[extra_field_len]',
            'compressed_data' => 'str[compressed_size]',
        ];

        $test2 = Bin::unpack($pkZipFormat, $fileContents, $offset);
//        var_dump($test2);exit;

        $this->assertEquals(54, $offset);
        $this->assertEquals("PK\x03\x04", $test2['signature'], "String, fixed length");
        $this->assertEquals(10, $test2['version'], "Little endian unsigned int16");
        $this->assertEquals('test2.txt', $test2['filename'], "String, variable length");

        $test3 = Bin::unpack($pkZipFormat, $fileContents, $offset);
        $this->assertEquals('the third and final test file!', $test3['compressed_data'], "Starting offset worked correctly");

        $test1 = Bin::unpack(['str[4]', '-uint16'], $fileContents, $offset);
        $this->assertEquals(["PK\x03\x04", 10], $test1, "Unpack using numeric array");

        $result = Bin::unpack(['@26', '0len' => '-uint16', '@+2', 'name' => 'str[0len]'], $fileContents);
        $this->assertEquals('test2.txt', $result['name'], "Offsets and weird key names");
    }

    function testPack() {
        $fileContents = file_get_contents(__DIR__ . '/unpack.zip');
        $test2 = substr($fileContents, 0, 54);

        $pkZipFormat = ['str', '-uint16{5}', '-uint32{3}', '-uint16{2}', 'str{3}'];
        $this->assertSame($test2, Bin::pack($pkZipFormat, [
            "PK\x03\x04",
            [10,0,0,27023,17644],
            [4228488003,[15,15]],
            9,0,
            'test2.txt','','a 2nd test file'
        ]), "Repeat and flatten");

        if(PHP_INT_SIZE >= 8) {
            $this->assertSame("\x9C\xCF\x29\xF3\x39\x94\x06\x9B", Bin::pack('-uint64', '11170778902352744348'), "MS-FSSHTTPB request signature");
            $this->assertSame("\x9B\x06\x94\x39\xF3\x29\xCF\x9C", Bin::pack('+uint64', '11170778902352744348'), "Big endian unsigned int64");
            $this->assertSame("\xBC\x0A\x00\x00\x00\x00\x00\x00", Bin::pack('-uint64', 0xABC));
            $this->assertSame("\x00\x00\x00\x00\x00\x00\x0A\xBC", Bin::pack('+uint64', 0xABC));
            $this->assertSame("\xCA\xFE\xBA\xBE\xDE\xAD\xBE\xEF", Bin::pack(['-uint32', '+uint32'], ['3199925962', '3735928559']));
            $this->assertSame("\xBC\x0A\x00\x00\x00\x00", Bin::pack('-uint48', 0xABC));
            $this->assertSame("\x00\x00\x00\x00\x0A\xBC", Bin::pack('+uint48', 0xABC));
        }

        $this->assertEquals(hex2bin('ffff0000'), Bin::pack('-float', 9.183409485952689E-41),'',1.e-100);
        $this->assertEquals(hex2bin('ffff0000'), Bin::pack('-float32', 9.183409485952689E-41),'',1.e-100);

        $this->assertEquals(hex2bin('0000ffff'), Bin::pack('+float', 9.183409485952689E-41),'',1.e-100);
        $this->assertEquals(hex2bin('0000ffff'), Bin::pack('+float32', 9.183409485952689E-41),'',1.e-100);

        $this->assertEquals(hex2bin('ffffffff00000000'), Bin::pack('-float64', 2.1219957905E-314),'',1.e-100);
        $this->assertEquals(hex2bin('ffffffff00000000'), Bin::pack('-double', 2.1219957905E-314),'',1.e-100);

        $this->assertEquals(hex2bin('00000000ffffffff'), Bin::pack('+float64', 2.1219957905E-314),'',1.e-100);
        $this->assertEquals(hex2bin('00000000ffffffff'), Bin::pack('+double', 2.1219957905E-314),'',1.e-100);
    }

    function testPackPolygon() {
        $polyFormat = [
            'srid' => '-uint32',
            'byteorder' => 'byte',
            'type' => '-uint32',
            'unknown' => '-uint32',
            'length' => '-uint32',
        ];

        $bin = Bin::pack($polyFormat, [
            'srid' => 0,
            'byteorder' => 1,
            'type' => 3,
            'unknown' => 1,
            'length' => 3,
        ]);
        $bin .= Bin::pack('-double{6}', [1, 1, 2, 2, 1, 1]);

        $this->assertSame(hex2bin('0000000001030000000100000003000000000000000000F03F000000000000F03F00000000000000400000000000000040000000000000F03F000000000000F03F'), $bin);
    }

    function testLength() {
        $this->assertSame(0, Bin::length(''));
        $this->assertSame(36, Bin::length('thequickbrownfoxjumpedoverthelazydog'));
        $this->assertSame(24, Bin::length('Cién cañones por banda'));
    }

    function testHasFlag() {
        $this->assertTrue(Bin::hasFlag(0b111, 0b010));
        $this->assertFalse(Bin::hasFlag(0b101, 0b010));
        $this->assertTrue(Bin::hasFlag(0b111, 0b110));
        $this->assertFalse(Bin::hasFlag(0b101, 0b110));
    }
}
