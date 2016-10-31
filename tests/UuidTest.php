<?php
use Ptilz\Uuid;

class UuidTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function testUuid() {
        $found = [];
        $uuids = [];
        for($i=0; $i<2500; ++$i) { // on avergae, it seems to take about 200 iterations to collect all 32 chars for each position
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
}
