<?php
use Ptilz\Path;

class PathTest extends PHPUnit_Framework_TestCase {

    function testIsAbsolute() {
        Path::setWindowsMode(false);

        $this->assertTrue(Path::isAbsolute('/'));
        $this->assertTrue(Path::isAbsolute('/foo'));
        $this->assertTrue(Path::isAbsolute('/foo/bar'));
        $this->assertTrue(Path::isAbsolute('/foo/bar/'));
        $this->assertTrue(Path::isAbsolute('/foo/bar/..'));

        $this->assertFalse(Path::isAbsolute('foo'));
        $this->assertFalse(Path::isAbsolute('foo/'));
        $this->assertFalse(Path::isAbsolute('foo/bar'));
        $this->assertFalse(Path::isAbsolute('./baz'));

        Path::setWindowsMode(true);

//        $this->assertTrue(Path::isAbsolute('c:'));
//        $this->assertTrue(Path::isAbsolute('D:'));
        $this->assertTrue(Path::isAbsolute('e:\\'));
        $this->assertTrue(Path::isAbsolute('F:/'));
        $this->assertTrue(Path::isAbsolute('g:\\foo'));
        $this->assertTrue(Path::isAbsolute('\\\\server\file'));
        $this->assertTrue(Path::isAbsolute('//server\file'));

        $this->assertFalse(Path::isAbsolute('c:foo/bar'));
        $this->assertFalse(Path::isAbsolute('c:foo\\bar'));
        $this->assertFalse(Path::isAbsolute('foo'));
        $this->assertFalse(Path::isAbsolute('foo\\'));
        $this->assertFalse(Path::isAbsolute('foo/bar'));
        $this->assertFalse(Path::isAbsolute('foo\\bar'));
    }

    function testRelative() {
        Path::setWindowsMode(false);
        $this->assertEquals('../../impl/bbb', Path::relative('/data/orandea/test/aaa', '/data/orandea/impl/bbb'));
        $this->assertEquals('../../../bbb/ccc/ddd', Path::relative('/aaa/bbb/ccc', '/bbb/ccc/ddd'));
        $this->assertEquals('baz/file.js', Path::relative('/foo/bar', '/foo/bar/baz/file.js'));

        Path::setWindowsMode(true);
        $this->assertEquals('..\\..\\impl\\bbb', Path::relative('C:\\orandea\\test\\aaa', 'C:\\orandea\\impl\\bbb'));
        $this->assertEquals('baz\\file.js', Path::relative('c:/foo/bar', 'c:/foo/bar/baz/file.js'));
        $this->assertEquals('baz\\file.js', Path::relative('c:\\foo\\bar', 'c:\\foo\\bar\\baz\\file.js'));
        $this->assertEquals('dir\\file.js', Path::relative('\\\\host\\root', '\\\\host\\root\\dir\\file.js'));
    }

    function testJoin() {
        Path::setWindowsMode(false);
        $this->assertSame("a/b", Path::join('a', 'b'));
        $this->assertSame("a/b", Path::join('a', '/b'));
        $this->assertSame("a/b", Path::join('a/', '/b\\'));
        $this->assertSame("foo/bar/baz", Path::join('foo', 'bar\\baz'));

        Path::setWindowsMode(true);
        $this->assertSame("a\\b", Path::join('a', 'b'));
        $this->assertSame("a\\b", Path::join('a', '\\b'));
        $this->assertSame("a\\b", Path::join('a/', '/b\\'));
        $this->assertSame("foo\\bar\\baz", Path::join('foo', 'bar/baz'));
    }

    function testResolve() {
        Path::setWindowsMode(false);
        $this->assertSame('/foo/bar/baz', Path::resolve('/foo/bar', 'baz'));
        $this->assertSame('/foo/bar/baz', Path::resolve(__DIR__, '/foo/bar', 'baz'));
        $this->assertSame('/foo/bar/baz/img.jpg', Path::resolve('/foo/bar', 'baz', 'img.jpg'));
        $this->assertSame('/foo/bar/c:/baz/img.jpg', Path::resolve('/foo/bar', 'c:/baz', 'img.jpg'));

        Path::setWindowsMode(true);
        $this->assertSame('\\foo\\bar\\baz', Path::resolve('/foo/bar', 'baz'));
        $this->assertSame('\\foo\\bar\\baz', Path::resolve(__DIR__, '/foo/bar', 'baz'));
        $this->assertSame('\\foo\\bar\\baz\\img.jpg', Path::resolve('/foo/bar', 'baz', 'img.jpg'));
        $this->assertSame('c:\\baz\\img.jpg', Path::resolve('/foo/bar', 'c:/baz', 'img.jpg'));
    }

    function testNormalize() {
        Path::setWindowsMode(false);
        $this->assertSame('/baz', Path::normalize('/foo/bar/../../baz'));
        $this->assertSame('/foo/bar', Path::normalize('/foo/bar/baz/..'));
        $this->assertSame('\\foo\\bar', Path::normalize('/foo/bar/baz/..', '\\'));
        $this->assertSame('/foo/bar/baz', Path::normalize('/foo/bar/baz/.'));
        $this->assertSame('foo/bar', Path::normalize('foo//bar/'));
        $this->assertSame('../baz', Path::normalize('foo/../../bar/../baz'));
        $this->assertSame('../bax', Path::normalize('foo/../../bar/../baz/../bax'));
        $this->assertSame('../../bax', Path::normalize('foo/../../bar/../baz/../../bax'));
        $this->assertSame('.', Path::normalize('foo/bar/../..'));
        $this->assertSame('..', Path::normalize('foo/bar/../../..'));
        $this->assertSame('../..', Path::normalize('foo/bar/../../../..'));
        $this->assertSame('/', Path::normalize('/foo/bar/../../'));
        $this->assertSame('/', Path::normalize('/foo/bar/../../..'));
        $this->assertSame('/baz', Path::normalize('/foo/bar/../../../baz'));

        Path::setWindowsMode(true);
        $this->assertSame('c:\\baz', Path::normalize('c:/foo/bar/../../baz'));
        $this->assertSame('\\foo\\bar', Path::normalize('\\foo\\bar/baz/..'));
        $this->assertSame('/foo/bar', Path::normalize('\\foo\\bar/baz/..','/'));
        $this->assertSame('\\foo\\bar\\baz', Path::normalize('/foo/bar/baz/.'));
        $this->assertSame('foo\\bar', Path::normalize('foo//bar/'));
        $this->assertSame('.', Path::normalize('foo/bar/../..'));
        $this->assertSame('..', Path::normalize('foo/bar/../../..'));
        $this->assertSame('..\\..', Path::normalize('foo/bar/../../../..'));
        $this->assertSame('C:\\', Path::normalize('C:/foo/bar/../../'));
        $this->assertSame('C:\\', Path::normalize('C:/foo/bar/../../..'));
        $this->assertSame('C:\\baz', Path::normalize('C:/foo/bar/../../../baz'));
        $this->assertSame('\\\\MARK-MAIN\\Users\\Mark\\.thumbnails\\fail', Path::normalize('\\\\MARK-MAIN\\Users\\Mark\\.thumbnails\\fail'));
        $this->assertSame('\\\\MARK-MAIN\\Users\\Mark\\.thumbnails\\normal', Path::normalize('\\\\MARK-MAIN\\Users\\Mark\\.thumbnails\\fail\\..\\normal'));
        $this->assertSame('\\\\MARK-MAIN/Users/Mark/.thumbnails/normal', Path::normalize('\\\\MARK-MAIN\\Users\\Mark\\.thumbnails\\fail\\..\\normal','/')); // works in Explorer but not pushd
        $this->assertSame('\\\\MARK-MAIN\\Users\\Public', Path::normalize('\\\\MARK-MAIN\\Users\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\..\\Public')); // pushd mounts \\MARK-MAIN\\Users to a drive letter and then prevents cd'ing above this level
    }
}