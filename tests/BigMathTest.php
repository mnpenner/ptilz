<?php
use Ptilz\BigMath;
use Ptilz\Exceptions\ArgumentOutOfRangeException;
use Ptilz\Iter;
use Ptilz\Math;

class BigMathTest extends PHPUnit_Framework_TestCase {
    const EPSILON = 0.0000000005;

    function testLn() {
        $this->assertEquals(2,BigMath::log(100),'',self::EPSILON);
        $this->assertEquals(2,BigMath::ln(M_E*M_E),'',self::EPSILON);

        foreach(Iter::take(Iter::skip(Iter::fibonacci(),1),25) as $n) {
            $this->assertEquals(log($n,10), BigMath::log($n,10));
            $this->assertEquals(log($n), BigMath::ln($n));
            $this->assertEquals(log($n,5), BigMath::log($n,5));
            $this->assertEquals(log($n,2), BigMath::log($n,2));
            $this->assertEquals(log($n,M_E), BigMath::ln($n));
        }

        $this->assertEquals(95.437646055703639312251403018260590807274066199998,BigMath::ln('280571172992510140037611932413038677189525'),'',self::EPSILON);
        $this->assertEquals(41.448043047827737351461287294873329877875942924373,BigMath::log('280571172992510140037611932413038677189525',10),'',self::EPSILON);

        $this->assertEquals(480.407106103,BigMath::ln('43466557686937456435688527675040625802564660517371780402481729089536555417949051890403879840079255169295922593080322634775209689623239873322471161642996440906533187938298969649928516003704476137795166849228875'),'',self::EPSILON);
        $this->assertEquals(208.638155248,BigMath::log('43466557686937456435688527675040625802564660517371780402481729089536555417949051890403879840079255169295922593080322634775209689623239873322471161642996440906533187938298969649928516003704476137795166849228875',10),'',self::EPSILON);

//        $this->assertEquals('4.605170185988091368035982909368728415202202977257545952066655801935145219354704960471994410179196597',BigMath::ln(100,100));
        // http://www.wolframalpha.com/input/?i=N%5Blog+10+base+10%2C+50%5D
        // http://stackoverflow.com/questions/24945193/fast-arbitrary-precision-logarithms-with-bcmath
    }
}