<?php

use Ptilz\BinaryStream;

class BinaryStreamTest extends PHPUnit_Framework_TestCase {


    function testDisplay() {
        $this->assertSame("00000000 00000000 00000000 00001111",BinaryStream::binrep("\x0F"));
        $this->assertSame("0F",BinaryStream::bin2uhex("\x0F"));
        $this->assertSame("F0",BinaryStream::bin2uhex("\xF0"));
    }

    function testRead() {
        $stream = new BinaryStream("\x0F");
        $this->assertSame(1,$stream->readBits());
        $this->assertSame(7,$stream->readBits(3));
        $this->assertSame(0,$stream->readBits(4));
    }
}