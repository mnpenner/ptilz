<?php
use Ptilz\Iter;

class BootstrapTest extends PHPUnit_Framework_TestCase {
    function testIntdiv() {
        $this->assertSame(1,intdiv(3,2));
        $this->assertSame(-1,intdiv(-3,2));
        $this->assertSame(-1,intdiv(3,-2));
        $this->assertSame(1,intdiv(-3,-2));
        $this->assertSame(1,intdiv(PHP_INT_MAX,PHP_INT_MAX));
        $this->assertSame(1,intdiv(-PHP_INT_MAX-1,-PHP_INT_MAX-1));
        $this->assertSame(0,intdiv(-PHP_INT_MAX-1,-1));
        $this->assertSame(-3,intdiv(-10,3));
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    function testInddiv0() {
        $this->assertFalse(intdiv(1,0));
    }
}