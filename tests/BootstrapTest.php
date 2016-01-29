<?php

class BootstrapTest extends PHPUnit_Framework_TestCase {
    function testIntdiv() {
        $this->assertSame(1,intdiv(3,2));
        $this->assertSame(-1,intdiv(-3,2));
        $this->assertSame(-1,intdiv(3,-2));
        $this->assertSame(1,intdiv(-3,-2));
        $this->assertSame(1,intdiv(PHP_INT_MAX,PHP_INT_MAX));
        $this->assertSame(1,intdiv(-PHP_INT_MAX-1,-PHP_INT_MAX-1));
        $this->assertSame(-3,intdiv(-10,3));
    }


    function testInddiv0() {
        $this->setExpectedException(DivisionByZeroError::class);
        $this->assertFalse(intdiv(1, 0));
    }

    function testInddiv1() {
        $this->setExpectedException(ArithmeticError::class);
        $this->assertFalse(intdiv(PHP_INT_MIN, -1));
    }
}