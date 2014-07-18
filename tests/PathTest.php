<?php
use Ptilz\Path;

class PathTest extends PHPUnit_Framework_TestCase {

    function testIsAbsolute() {
        Path::setWindows(false);

        $this->assertTrue(Path::isAbsolute('/'));
        $this->assertTrue(Path::isAbsolute('/foo'));
        $this->assertTrue(Path::isAbsolute('/foo/bar'));
        $this->assertTrue(Path::isAbsolute('/foo/bar/'));
        $this->assertTrue(Path::isAbsolute('/foo/bar/..'));

        $this->assertFalse(Path::isAbsolute('foo'));
        $this->assertFalse(Path::isAbsolute('foo/'));
        $this->assertFalse(Path::isAbsolute('foo/bar'));
        $this->assertFalse(Path::isAbsolute('./baz'));

        Path::setWindows(true);

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
        Path::setWindows(false);
        $this->assertEquals('../../impl/bbb', Path::relative('/data/orandea/test/aaa', '/data/orandea/impl/bbb'));
        $this->assertEquals('../../../bbb/ccc/ddd', Path::relative('/aaa/bbb/ccc', '/bbb/ccc/ddd'));
        $this->assertEquals('baz/file.js', Path::relative('/foo/bar', '/foo/bar/baz/file.js'));

        Path::setWindows(true);
        $this->assertEquals('..\\..\\impl\\bbb', Path::relative('C:\\orandea\\test\\aaa', 'C:\\orandea\\impl\\bbb'));
        $this->assertEquals('baz\\file.js', Path::relative('c:/foo/bar', 'c:/foo/bar/baz/file.js'));
        $this->assertEquals('baz\\file.js', Path::relative('c:\\foo\\bar', 'c:\\foo\\bar\\baz\\file.js'));
        $this->assertEquals('dir\\file.js', Path::relative('\\\\host\\root', '\\\\host\\root\\dir\\file.js'));
    }

    function testJoin() {
        Path::setWindows(false);
        $this->assertSame("a/b", Path::join('a', 'b'));
        $this->assertSame("a/b", Path::join('a', '/b'));
        $this->assertSame("a/b", Path::join('a/', '/b\\'));
        $this->assertSame("foo/bar/baz", Path::join('foo', 'bar\\baz'));

        Path::setWindows(true);
        $this->assertSame("a\\b", Path::join('a', 'b'));
        $this->assertSame("a\\b", Path::join('a', '\\b'));
        $this->assertSame("a\\b", Path::join('a/', '/b\\'));
        $this->assertSame("foo\\bar\\baz", Path::join('foo', 'bar/baz'));
    }

    function testResolve() {
        Path::setWindows(false);
        $this->assertSame('/foo/bar/baz', Path::resolve('/foo/bar', 'baz'));
        $this->assertSame('/foo/bar/baz', Path::resolve(__DIR__, '/foo/bar', 'baz'));
        $this->assertSame('/foo/bar/baz/img.jpg', Path::resolve('/foo/bar', 'baz', 'img.jpg'));
        $this->assertSame('/foo/bar/c:/baz/img.jpg', Path::resolve('/foo/bar', 'c:/baz', 'img.jpg'));

        Path::setWindows(true);
        $this->assertSame('\\foo\\bar\\baz', Path::resolve('/foo/bar', 'baz'));
        $this->assertSame('\\foo\\bar\\baz', Path::resolve(__DIR__, '/foo/bar', 'baz'));
        $this->assertSame('\\foo\\bar\\baz\\img.jpg', Path::resolve('/foo/bar', 'baz', 'img.jpg'));
        $this->assertSame('c:\\baz\\img.jpg', Path::resolve('/foo/bar', 'c:/baz', 'img.jpg'));
    }

    function testNormalize() {
        Path::setWindows(false);
        $this->assertSame('/baz', Path::normalize('/foo/bar/../../baz'));
        $this->assertSame('/foo/bar', Path::normalize('/foo/bar/baz/..'));
        $this->assertSame('/foo/bar/baz', Path::normalize('/foo/bar/baz/.'));
        $this->assertSame('foo/bar', Path::normalize('foo//bar/'));

        Path::setWindows(true);
        $this->assertSame('c:\\baz', Path::normalize('c:/foo/bar/../../baz'));
        $this->assertSame('\\foo\\bar', Path::normalize('\\foo\\bar/baz/..'));
        $this->assertSame('\\foo\\bar\\baz', Path::normalize('/foo/bar/baz/.'));
        $this->assertSame('foo\\bar', Path::normalize('foo//bar/'));
    }
}