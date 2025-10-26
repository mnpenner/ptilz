<?php
use Ptilz\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase {

    /**
     * @dataProvider  dataSplitFileName
     */
    public function testSplitFileName($exp, ...$args) {
        $this->assertSame($exp, File::splitFileName(...$args));
    }

    public function dataSplitFileName() {
        return [
            [['foo', '.bar'], 'foo.bar', true],
            [['foo', '.tar.gz'], 'foo.tar.gz', true],
            [['foo.tar', '.gz'], 'foo.tar.gz', false],
            [['foo.bar', '.tar.gz'], 'foo.bar.tar.gz', true],
            [['.hgignore', ''], '.hgignore', true],
            [['', ''], '', true],
            [['.', ''], '.', true],
            [['..', ''], '..', true],
            [['foo.', '.gz'], 'foo..gz', true],
        ];
    }
}
