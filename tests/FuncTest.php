<?php
use Ptilz\Func;
use Ptilz\Iter;

class FuncTest extends PHPUnit_Framework_TestCase {
    function testArity() {
        $this->assertSame(3, Func::arity(function($a,$b,$c){}));
        $this->assertSame(1, Func::arity('is_numeric'));
    }
}