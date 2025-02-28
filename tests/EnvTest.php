<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;

use Bidoch78\Bimi\Core\Env;
use Bidoch78\Bimi\Storage\StorageFile;

#[CoversClass(Env::class)]
final class EnvTest extends TestCase {

    #[TestDox('Test from .env')]
    public function testEnvFile() {

        Env::setFilePath(new StorageFile(realpath(__DIR__ . "/../tests_folder/env/test.env"))); 
        $this->assertEquals(Env::get("param1"), "result param1");
        $this->assertEquals(Env::get("param2"), "12345");
        $this->assertEquals(Env::get("param3"), null);
        $this->assertEquals(Env::get("param4"), "param value");

    }


}