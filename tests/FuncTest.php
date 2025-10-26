<?php
use Ptilz\Func;
use Ptilz\Iter;
use PHPUnit\Framework\TestCase;

class FuncTest extends TestCase {
    function testArity() {
        $this->assertSame(3, Func::arity(function($a,$b,$c){}));
        $this->assertSame(1, Func::arity('is_numeric'));
        $this->assertSame(2, Func::arity(_TestClass::class.'::publicMethod'));
        $this->assertSame(3, Func::arity(_TestClass::class.'::publicStatic'));
        $this->assertSame(2, Func::arity(_TestClass::class,'publicMethod'));
        $this->assertSame(3, Func::arity(_TestClass::class,'publicStatic'));
        $obj = new _TestClass;
        $this->assertSame(2, Func::arity($obj,'publicMethod'));
        $this->assertSame(3, Func::arity($obj,'publicStatic'));
    }

    function testInvokeMethod() {
        $obj = new _TestClass;
        $this->assertSame(1,Func::invokeMethod($obj,'_privateMethod',1));
        $this->assertSame(2,Func::invokeMethodArgs($obj,'_privateMethod',[2]));
        $this->assertSame(3,Func::invokeMethod(_TestClass::class,'_privateStatic',3));
        $this->assertSame(4,Func::invokeMethodArgs(_TestClass::class,'_privateStatic',[4]));
    }


    function testGetReflection() {
        $obj = new _TestClass;

        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(function($x){}));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection('is_numeric'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(__METHOD__));

        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(_TestClass::class.'::publicMethod'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(_TestClass::class.'::publicStatic'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(_TestClass::class,'publicMethod'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(_TestClass::class,'publicStatic'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(_TestClass::class,'_privateMethod'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection(_TestClass::class,'_privateStatic'));

        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection($obj,'publicMethod'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection($obj,'publicStatic'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection($obj,'_privateMethod'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection($obj,'_privateStatic'));

        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([_TestClass::class,'publicMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([_TestClass::class,'publicStatic']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([_TestClass::class,'_privateMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([_TestClass::class,'_privateStatic']));

        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([$obj,'publicMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([$obj,'publicStatic']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([$obj,'_privateMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class,Func::getReflection([$obj,'_privateStatic']));
    }
}

class _TestClass {
    private function _privateMethod($val) {
        return $val;
    }

    private static function _privateStatic($val) {
        return $val;
    }

    public function publicMethod($a,$b) {}
    public static function publicStatic($a,$b,$c) {}
}