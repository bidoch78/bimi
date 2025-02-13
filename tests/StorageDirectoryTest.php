<?php

namespace Bidoch78\Bimi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;

use Bidoch78\Bimi\Storage\StorageDirectory;

#[CoversClass(StorageDirectory::class)]
final class StorageDirectoryTest extends TestCase {

    #[TestDox('Test StorageDirectory')]
    public function testValideDirectory() {

        $dir = new StorageDirectory(__DIR__);

        $this->assertEquals($dir->hasError(), false);
        $this->assertFalse($dir->isFile());
        $this->assertTrue($dir->isLocal());
        $this->assertFalse($dir->isLink());
        $this->assertTrue($dir->isDir());

        $this->assertEquals($dir->getName(), "tests");
        // $this->assertEquals($dir->getBaseName(), "test.env");
        $this->assertEquals($dir->getDirName(), realpath(__DIR__ . "/../"));
        $this->assertNull($dir->getExtension());
        $this->assertInstanceOf(\DateTime::class, $dir->getModifyTime());
        $this->assertInstanceOf(\DateTime::class, $dir->getCreationTime());
        $this->assertInstanceOf(\DateTime::class, $dir->getAccessTime());
        $this->assertIsInt($dir->getSize());
        $this->assertIsInt($dir->getUID());
        $this->assertIsInt($dir->getGID());
        $this->assertIsString($dir->getPermission());
        $this->assertIsString($dir->getPermission(\Bidoch78\Bimi\Storage\PERMISSION_STRING));

    }

}