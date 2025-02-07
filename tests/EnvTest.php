<?php

namespace Bidoch78\Bimi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;

use Bidoch78\Bimi\Core\Env;

#[CoversClass(Env::class)]
final class EnvTest extends TestCase {

    #[TestDox('Test from .env')]
    public function testEnvFile() {

        Env::setFilePath(__DIR__ . "/test.env"); 
        $this->assertEquals(Env::get("param1"), "result param1");
        $this->assertEquals(Env::get("param2"), "12345");
        $this->assertEquals(Env::get("param3"), null);
        $this->assertEquals(Env::get("param4"), "param value");

    }


}