<?php
use Ptilz\BigMath;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Math;

class BigMathTest extends PHPUnit_Framework_TestCase {
    const EPSILON = 0.0000000005;

    function testLn() {
        $this->assertEquals(2,BigMath::log(100),'',self::EPSILON);
        $this->assertEquals(2,BigMath::ln(M_E*M_E),'',self::EPSILON);
//        $this->assertEquals('4.605170185988091368035982909368728415202202977257545952066655801935145219354704960471994410179196597',BigMath::ln(100,100));
        // http://www.wolframalpha.com/input/?i=N%5Blog+10+base+10%2C+50%5D
        // http://stackoverflow.com/questions/24945193/fast-arbitrary-precision-logarithms-with-bcmath
    }
}