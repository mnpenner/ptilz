<?php
use Ptilz\Uuid;

class UuidTest extends PHPUnit_Framework_TestCase {
    
    public function testUuid() {
        $found = [];
        $uuids = [];
        for($i=0; $i<2500; ++$i) { // on average, it seems to take about 200 iterations to collect all 32 chars for each position
            $uuid = Uuid::unique();
            if(isset($uuids[$uuid])) {
                $this->fail("Duplicate UUID found");
            }
            $uuids[$uuid] = true;
            $this->assertRegExp('#[0123456789abcdefghjkmnpqrstvwxyz]{25}\z#A', $uuid);
            for($j=0; $j<strlen($uuid); ++$j) {
                if(!isset($found[$j])) {
                    $found[$j] = [];
                }
                $found[$j][$uuid[$j]] = true;
            }
            for($j=0; $j<strlen($uuid); ++$j) {
                if(count($found[$j]) !== 32) {
                    continue 2;
                }
            }
            break;
        }
        $this->assertCount(25, $found);
        for($i=0; $i<25; ++$i) {
            $this->assertCount(32, $found[$i]);
        }
    }

    public function testOuidUniqueness() {
        $found = [];
        $ouids = [];
        for($i=0; $i<1500; ++$i) { 
            $ouid = Uuid::ordered();
            if(isset($ouids[$ouid])) {
                $this->fail("Duplicate OUID found");
            }
            $ouids[$ouid] = true;
            $this->assertRegExp('#[0123456789abcdefghjkmnpqrstvwxyz]{30}\z#A', $ouid);
            for($j=0; $j<strlen($ouid); ++$j) {
                @$found[$j][$ouid[$j]] = true;
            }
        }
        $this->assertCount(30, $found);

        // the last 20 chars should be distributed evenly
        for($i=10; $i<30; ++$i) {
            $this->assertCount(32, $found[$i]);
        }
    }

    public function testOuidOrder() {
        $ouids = [];
        $t = random_int(0,2**47);
        for($i=0; $i<256; ++$i) {
            Uuid::setTestNow($t);
            $ouids[] = Uuid::ordered();
            ++$t;
        }
        Uuid::setTestNow(null);
        $sorted = $ouids;
        sort($sorted);
        $this->assertSame($ouids,$sorted,"Order should be exactly the same after sorting");
    }

    public function testExtractDate() {
        $utc = new DateTimeZone('UTC');
        $this->assertEquals(new DateTime('2016-10-31 21:13:07.187700', $utc), Uuid::extractDate('0de4ey5pm5qt1ceh1cvz63r85byr8w'));
        // $this->assertEquals(new DateTime("2016-10-31 21:13:07.187700"), Uuid::extractDate('0de4ey5pm5'));
        $this->assertEquals(new DateTime('2016-10-31 22:05:37.000000', $utc), Uuid::extractDate('0de4fw6yggr2yj71dwkzjw6rmqy8w0'));
    }

    public function testExtractDate2() {
        $now = DateTime::createFromFormat('U.u',microtime(true));
        $soon = new DateTime('+2 seconds');
        for($i=0; $i<256; ++$i) {
            $uuid = Uuid::ordered();
            $date = Uuid::extractDate($uuid);
            $this->assertInstanceOf(\DateTime::class,$date,"Ordered UUID: $uuid");
            $this->assertGreaterThanOrEqual($now, $date);
            $this->assertLessThan($soon, $date);
        }
    }

    public function testExtractDate3() {
        $now = new DateTime();
        $soon = new DateTime('+2 seconds');
        for($i=0; $i<256; ++$i) {
            $uuid = Uuid::binary();
            $date = Uuid::extractDate($uuid);
            $this->assertInstanceOf(\DateTime::class,$date,"Binary UUID: ".bin2hex($uuid));
            $this->assertGreaterThanOrEqual($now, $date);
            $this->assertLessThan($soon, $date);
        }
    }

    public function testBinaryDate() {
        Uuid::setTestNow(2**32-1);
        $this->assertStringStartsWith('0000ffffffff',bin2hex(Uuid::binary()));
        Uuid::setTestNow(2**32);
        $this->assertStringStartsWith('000100000000',bin2hex(Uuid::binary()));
        Uuid::setTestNow(2**48-1);
        $this->assertStringStartsWith('ffffffffffff',bin2hex(Uuid::binary()));
        Uuid::setTestNow(null);
    }

    public function testBinaryOrder() {
        $ouids = [];
        $t = random_int(0,2**47);
        for($i=0; $i<256; ++$i) {
            Uuid::setTestNow($t);
            $ouids[] = Uuid::binary();
            ++$t;
        }
        Uuid::setTestNow(null);
        $sorted = $ouids;
        sort($sorted);
        $this->assertSame($ouids,$sorted,"Order should be exactly the same after sorting");
    }

    public function testBinaryUniqueness() {
        $found = [];
        $uuids = [];
        for($i=0; $i<1500; ++$i) {
            $uuid = Uuid::binary();
            if(isset($uuids[$uuid])) {
                $this->fail("Duplicate OUID found");
            }
            $uuids[$uuid] = true;
            for($j=0; $j<strlen($uuid); ++$j) {
                @$found[$j][$uuid[$j]] = true;
            }
        }
        $this->assertCount(20, $found);

        // the last 14 chars should be distributed evenly
        for($i=6; $i<20; ++$i) {
            // all 256 chars should be used, but it's random so this is hard to test!
            $this->assertGreaterThanOrEqual(200, count($found[$i]));
        }
    }
}
