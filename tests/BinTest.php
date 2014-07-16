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
//            $this->assertEquals(1<<24, Bin::unpack('uint', "\x00\x00\x00\x01"));
        } else {
            $this->assertEquals(1<<8, Bin::unpack('uint16', "\x01\x00"));
//            $this->assertEquals(1<<24, Bin::unpack('uint', "\x01\x00\x00\x00"));
        }

        $this->assertEquals('HELO', Bin::unpack('str[4]', "HELO"));
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

        $pkZipFormat = ['str', '-uint16{5}', '-uint32{3}', '-uint16{2}', 'str', 'str', 'str'];
        $this->assertEquals($test2, Bin::pack($pkZipFormat, [
            "PK\x03\x04",
            10,
            0,
            0,
            27023,
            17644,
            4228488003,
            15,
            15,
            9,
            0,
            'test2.txt',
            '',
            'a 2nd test file'
        ]));
    }
}