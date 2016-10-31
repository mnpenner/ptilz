<?php
use Ptilz\Uuid;

class UuidTest extends PHPUnit_Framework_TestCase {
    
    public function testUuid() {
        $found = [];
        $uuids = [];
        for($i=0; $i<2500; ++$i) { // on average, it seems to take about 200 iterations to collect all 32 chars for each position
            $uuid = Uuid::uuid();
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
            $ouid = Uuid::ouid();
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
        for($i=0; $i<2500; ++$i) {
            $ouids[] = Uuid::ouid();
            usleep(101); // order is only guaranteed if you generate one every 100+ microseconds
        }
        $sorted = $ouids;
        sort($sorted);
        $this->assertSame($ouids,$sorted,"Order should be exactly the same after sorting");
    }

    public function testExtractDate() {
        $this->assertEquals(new DateTime("2016-10-31 21:13:07.187700"), Uuid::extractDate('0de4ey5pm5qt1ceh1cvz63r85byr8w'));
        $this->assertEquals(new DateTime("2016-10-31 21:13:07.187700"), Uuid::extractDate('0de4ey5pm5'));
        $this->assertEquals(new DateTime('2016-10-31 22:05:37.000000'), Uuid::extractDate('0de4fw6yggr2yj71dwkzjw6rmqy8w0'));
    }

    public function testExtractDate2() {
        $now = new DateTime();
        $soon = new DateTime('+2 seconds');
        for($i=0; $i<1000; ++$i) {
            $ouid = Uuid::ouid();
            $date = Uuid::extractDate($ouid);
            $this->assertInstanceOf(\DateTime::class,$date,"OUID: $ouid");
            $this->assertGreaterThanOrEqual($now, $date);
            $this->assertLessThan($soon, $date);
        }
    }
}
