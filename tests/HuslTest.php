<?php
use Ptilz\Color;
use Ptilz\Math;

class HuslTest extends PHPUnit_Framework_TestCase {

    function testHuslToRgb() {
        $tests = \Ptilz\Json::loadFile(__DIR__ . '/husl-rev4.json');
        foreach($tests as $hex => $colorspaces) {
            $this->assertEquals($colorspaces['rgb'], Color::huslToRgb(...$colorspaces['husl']), $hex);
        }
    }
}
