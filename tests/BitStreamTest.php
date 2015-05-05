<?php


use Ptilz\BitStream;

class BitStreamTest extends PHPUnit_Framework_TestCase {


    function test1() {
        $stream = new BitStream(chr(0b111));
        $this->assertSame(7,$stream->read(3));
    }

    function test2() {
        $stream = new BitStream(chr(0b101));
        $this->assertSame(5,$stream->read(3));
    }

    function test3() {
        $stream = new BitStream(chr(0b00001111));
        //echo PHP_EOL.$stream;
        $this->assertSame(15,$stream->read(4));
        $this->assertSame(0,$stream->read(4));

    }

    function test4() {
        $stream = new BitStream(chr(0b01010101)); // 0101 0101
        $this->assertSame(1,$stream->read(2));
        $this->assertSame(21,$stream->read(6));
    }

    function test5() {
        $stream = new BitStream(chr(0b01010101).chr(0b01010101));
        $this->assertSame(0b010101,$stream->read(6));
        $this->assertSame(0b0101,$stream->read(4));
        $this->assertSame(0b010101,$stream->read(6));
    }

    function test6() {
        $stream = new BitStream(chr(0b0000000).chr(0b00000011));
        $this->assertSame(0b000000000000011,$stream->read(16));
    }

    function test7() {
        $stream = new BitStream(chr(0b1100000).chr(0b00000000));
        $this->assertSame(0b110000000000000,$stream->read(16));
    }

    function test8() {
        $stream = new BitStream(chr(0b00000001).chr(0b00000011));
        $this->assertSame(0b0000000100000011,$stream->read(16));
    }

    function test9() {
        $stream = new BitStream(chr(0b00000001).chr(0b10000000));
        $this->assertSame(0b0000000110000000,$stream->read(16));
    }

    function test10() {
        $stream = new BitStream(chr(0b00000100).chr(0b01100000));
        $this->assertSame(0b100,$stream->read(3));
        $this->assertSame(0b0000001100000,$stream->read(13));
    }
}