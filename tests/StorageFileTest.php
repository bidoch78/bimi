<?php

namespace Bidoch78\Bimi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;

use Psr\Http\Message\StreamInterface;
use Bidoch78\Bimi\Storage\StorageFile;

#[CoversClass(StorageFile::class)]
final class StorageFileTest extends TestCase {

    #[TestDox('Test StorageFile')]
    public function testValideFile() {

        $file = new StorageFile(__DIR__ . "/test.env");

        $this->assertEquals($file->hasError(), false);
        $this->assertTrue($file->isFile());
        $this->assertTrue($file->isLocal());
        $this->assertFalse($file->isLink());
        $this->assertEquals($file->isDir(), false);
        $this->assertEquals($file->getName(), "test");
        $this->assertEquals($file->getBaseName(), "test.env");
        $this->assertEquals($file->getDirName(), __DIR__);
        $this->assertEquals($file->getExtension(), "env");
        $this->assertInstanceOf(\DateTime::class, $file->getModifyTime());
        $this->assertInstanceOf(\DateTime::class, $file->getCreationTime());
        $this->assertInstanceOf(\DateTime::class, $file->getAccessTime());
        $this->assertIsInt($file->getSize());
        $this->assertIsInt($file->getUID());
        $this->assertIsInt($file->getGID());
        $this->assertIsString($file->getPermission());
        $this->assertIsString($file->getPermission(\Bidoch78\Bimi\Storage\PERMISSION_STRING));

    }

    #[TestDox('Test StorageFileStream')]
    public function testStreamFile() {

        $file = new StorageFile(__DIR__ . "/test.env");

        $stream = $file->getStream();

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertIsBool($stream->isWritable());
        $this->assertIsBool($stream->isSeekable());
        $this->assertIsBool($stream->isReadable());

        $this->assertIsArray($stream->getMetadata());
        $this->assertIsString($stream->getMetadata("uri"));
        $this->assertNull($stream->getMetadata("uri_notexists_metadata"));

        $this->assertIsInt($stream->getSize());

        $stream->close();

    }

}