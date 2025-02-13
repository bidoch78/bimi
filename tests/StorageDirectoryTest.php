<?php

namespace Bidoch78\Bimi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;

use Bidoch78\Bimi\Storage\StorageDirectory;

#[CoversClass(StorageDirectoy::class)]
#[CoversClass(StorageAbstract::class)]
#[CoversClass(StorageDirectoryAbstract::class)]
final class StorageDirectoryTest extends TestCase {

    private static $_testFolderPath = null;
    private static $_workOnPath = null;

    public function setUp(): void {
        self::$_testFolderPath = realpath(__DIR__ . "/../tests_folder");
        self::$_workOnPath = self::$_testFolderPath . "/work_on";
        if (!is_dir(self::$_workOnPath)) { mkdir(self::$_workOnPath); }
    }

    public function tearDown(): void {
       //if (is_dir(self::$_workOnPath)) rmdir(self::$_workOnPath);
    }

    #[TestDox('Test StorageDirectory properties')]
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

    #[TestDox('Test StorageDirectory manipulation')]
    public function testValideDirectory2() {

        $dir = new StorageDirectory(__DIR__);
        $this->assertEquals($dir->hasError(), false);
        
    }

}