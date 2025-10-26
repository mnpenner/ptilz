<?php


use Ptilz\BitStream;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase {
    /** @var \Ptilz\CsvReader::__construct */
    private $reader;

    /**
     * @covers \Ptilz\CsvReader::__construct
     */
    protected function setUp(): void {
        $this->reader = new \Ptilz\CsvReader(__DIR__ . '/sample.csv', true, 0, ',', '"', '\\', 0, "\r");
    }

    protected function tearDown(): void {
        unset($this->reader);
    }

    static $line1 = [
        'street' => '3526 HIGH ST',
        'city' => 'SACRAMENTO',
        'zip' => '95838',
        'state' => 'CA',
        'beds' => '2',
        'baths' => '1',
        'sq__ft' => '836',
        'type' => 'Residential',
        'sale_date' => 'Wed May 21 00:00:00 EDT 2008',
        'price' => '59222',
        'latitude' => '38.631913',
        'longitude' => '-121.434879',
    ];

    static $line2 = [
        'street' => '51 OMAHA CT',
        'city' => 'SACRAMENTO',
        'zip' => '95823',
        'state' => 'CA',
        'beds' => '3',
        'baths' => '1',
        'sq__ft' => '1167',
        'type' => 'Residential',
        'sale_date' => 'Wed May 21 00:00:00 EDT 2008',
        'price' => '68212',
        'latitude' => '38.478902',
        'longitude' => '-121.431028',
    ];

    static $lastLine = [
        'street' => '3882 YELLOWSTONE LN',
        'city' => 'EL DORADO HILLS',
        'zip' => '95762',
        'state' => 'CA',
        'beds' => '3',
        'baths' => '2',
        'sq__ft' => '1362',
        'type' => 'Residential',
        'sale_date' => 'Thu May 15 00:00:00 EDT 2008',
        'price' => '235738',
        'latitude' => '38.655245',
        'longitude' => '-121.075915',
    ];

    /**
     * @covers \Ptilz\CsvReader::readline
     */
    function testReadline() {
        $this->assertSame(self::$line1, $this->reader->readline());
        $this->assertSame(self::$line2, $this->reader->readline());
    }

    /**
     * @covers \Ptilz\CsvReader::rewind
     */
    function testRewind() {
        $this->reader->readline();
        $this->reader->readline();
        $this->reader->rewind();
        $this->assertSame(self::$line1, $this->reader->readline());
    }

    /**
     * @covers \Ptilz\CsvIterator::__construct
     * @covers \Ptilz\CsvIterator::next
     * @covers \Ptilz\CsvIterator::key
     * @covers \Ptilz\CsvIterator::valid
     * @covers \Ptilz\CsvIterator::current
     * @covers \Ptilz\CsvIterator::rewind
     */
    function testIterator() {
        $it = $this->reader->getIterator();
        foreach($it as $k => $v) {
            $this->assertSame(self::$line1, $v);
            $this->assertSame(0, $k);
            $this->assertTrue($it->valid());
            break;
        }
        foreach($it as $k => $v) { }
        $this->assertSame(self::$lastLine, $v);
        $this->assertSame(984, $k);
        $this->assertFalse($it->valid());
    }
}
