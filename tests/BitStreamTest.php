<?php


use Ptilz\BitStream;

class BitStreamTest extends PHPUnit_Framework_TestCase {




    function testRead() {
        $stream = new BitStream("\x0F"); // 0000 1111
        $this->assertSame(0,$stream->read(4));
        $this->assertSame(15,$stream->read(4));


        $stream = new BitStream("\x50"); // 0101 0101
        $this->assertSame(2,$stream->read(2));
        $this->assertSame(2,$stream->read(6));


        //dump(\Ptilz\BinaryStream::binrep("\x55\x55",16)); // 0101 0101

        $stream = new BitStream("\x55\x55");
        $this->assertSame(42,$stream->read(6));
        $this->assertSame(10,$stream->read(4));
        $this->assertSame(42,$stream->read(6));

        //echo "done\n";

        $stream = new BitStream("\x00\x03"); // 0000 0000 0000 0011
        $this->assertSame(49152,$stream->read(16));

        $stream = new BitStream("\x01\x03"); // 0000 0001 0000 0011
        $this->assertSame(49280,$stream->read(16));
    }
}