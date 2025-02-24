<?php

namespace Bidoch78\Bimi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;

use Psr\Http\Message\StreamInterface;
use Bidoch78\Bimi\Storage\StorageFile;

#[CoversClass(StorageFile::class)]
#[CoversClass(StorageAbstract::class)]
#[CoversClass(StorageFileAbstract::class)]
#[CoversClass(StorageFileStream::class)]
final class StorageFileTest extends TestCase {

    private static $_testFolderPath = null;
    private static $_workOnPath = null;

    public function setUp(): void {
        self::$_testFolderPath = realpath(__DIR__ . "/../tests_folder/storage");
        self::$_workOnPath = realpath(__DIR__ . "/../tests_folder/work_on");
        if (!is_dir(self::$_workOnPath)) { mkdir(self::$_workOnPath); }
        copy(self::$_testFolderPath . "/sample.pdf", self::$_workOnPath . "/sample.pdf");
    }

    public function tearDown(): void {
        //if (is_dir(self::$_workOnPath)) rmdir(self::$_workOnPath);
    }

    #[TestDox('Test StorageFile Properties')]
    public function testValideFile() {

        $filePath = self::$_workOnPath . "/sample.pdf";

        $file = new StorageFile($filePath);

        $this->assertEquals($file->hasError(), false);
        $this->assertTrue($file->exists());
        $this->assertTrue($file->isFile());
        $this->assertTrue($file->isLocal());
        $this->assertFalse($file->isLink());
        $this->assertEquals($file->isDir(), false);
        $this->assertEquals($file->getName(), "sample");
        $this->assertEquals($file->getBaseName(), "sample.pdf");
        $this->assertEquals($file->getDirName(), dirname($filePath));
        $this->assertEquals($file->getExtension(), "pdf");
        $this->assertInstanceOf(\DateTime::class, $file->getModifyTime());
        $this->assertInstanceOf(\DateTime::class, $file->getCreationTime());
        $this->assertInstanceOf(\DateTime::class, $file->getAccessTime());
        $this->assertIsInt($file->getSize());
        $this->assertIsInt($file->getUID());
        $this->assertIsInt($file->getGID());
        $this->assertIsString($file->getPermission());
        $this->assertIsString($file->getPermission(\Bidoch78\Bimi\Storage\PERMISSION_STRING));

    }

    #[TestDox('Test StorageFileStream Stream')]
    public function testStreamFile() {

        $filePath = self::$_workOnPath . "/sample.pdf";

        $file = new StorageFile($filePath);

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

    #[TestDox('Test StorageFileStream Manipulation')]
    public function testManipFile() {

        $filePath = self::$_workOnPath . "/sample.pdf";

        $file = new StorageFile($filePath);

        $this->assertIsBool($file->copy(new StorageFile(self::$_workOnPath . "/samplecopy.pdf")));

        $fileDest = new StorageFile(self::$_workOnPath . "/samplecopy.pdf");
        $this->assertEquals($file->getSize(), $fileDest->getSize(), "Size sample.pdf <> samplecopy.pdf");
        $this->assertEquals(crc32(file_get_contents(self::$_workOnPath . "/sample.pdf")), crc32(file_get_contents(self::$_workOnPath . "/samplecopy.pdf")), "CRC32 sample.pdf <> samplecopy.pdf");

        $file = new StorageFile($filePath . "2");
        $this->assertInstanceOf(StorageFile::class, $file->create());

        $this->assertIsBool($file->rename("toto.pdf"));

        $this->assertIsBool($file->delete());
        $this->assertFalse($file->exists());

        //create a file
        $string = <<< EOT
<xml>
    <node1></node1>
    <node2></node2>
</xml>
EOT;

        $newfile = new StorageFile(self::$_workOnPath . "/create.txt");
        $this->assertTrue($newfile->putContent($string));
        $this->assertTrue($newfile->exists());

        $this->assertEquals($newfile->getContent(), $string);

    }

}