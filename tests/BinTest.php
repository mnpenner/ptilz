<?php
use Ptilz\Bin;

class BinTest extends PHPUnit_Framework_TestCase {
    function testUnpack() {
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

        $this->assertSame(54, $offset);
        $this->assertSame("PK\x03\x04", $test2['signature'], "String, fixed length");
        $this->assertSame(10, $test2['version'], "Little endian unsigned int16");
        $this->assertSame('test2.txt', $test2['filename'], "String, variable length");

        $test3 = Bin::unpack($pkZipFormat, $fileContents, $offset);
        $this->assertSame('the third and final test file!', $test3['compressed_data'], "Starting offset worked correctly");

        $test1 = Bin::unpack(['str[4]', '-uint16'], $fileContents, $offset);
        $this->assertSame(["PK\x03\x04", 10], $test1, "Unpack using numeric array");

        $result = Bin::unpack(['@26', '0len' => '-uint16', '@+2', 'name' => 'str[0len]'], $fileContents);
        $this->assertSame('test2.txt', $result['name'], "Offsets and weird key names");
    }
}