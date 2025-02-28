<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;

use Bidoch78\Bimi\Trait\TraitString;

class TestString {
    use TraitString;
}

#[CoversClass(TraitString::class)]
final class TraitStringTest extends TestCase {

    #[TestDox('Test TraitString::explodeEx exception')]
    public function testExplodeExException1() {
        
        $str = new TestString();

        $this->expectException(\ValueError::class);
        $str->explodeEx(array(), "/toto\ppo//lolo/momo\\eorro");

    }

    #[TestDox('Test TraitString::explodeEx exception')]
    public function testExplodeExException2() {
        
        $str = new TestString();

        $this->expectException(\ValueError::class);
        $str->explodeEx(array(""), "/toto\ppo//lolo/momo\\eorro");

    }

    #[TestDox('Test TraitString::explodeEx exception')]
    public function testExplodeExException3() {
        
        $str = new TestString();

        $this->expectException(\ValueError::class);
        $str->explodeEx(array("", null), "/toto\ppo//lolo/momo\\eorro");

    }

    #[TestDox('Test TraitString::explodeEx return')]
    public function testExplodeEx() {

        $str = new TestString();

        $this->assertEquals($str->explodeEx(array("a", ""), "bab"), ["b", "b"]);
        
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro"), ["", "toto","ppo","","lolo","momo","eorro"]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", 0), ["/toto\ppo//lolo/momo\\eorro"]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", 1), ["/toto\ppo//lolo/momo\\eorro"]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", 2), ["", "toto\ppo//lolo/momo\\eorro"]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", 3), ["", "toto", "ppo//lolo/momo\\eorro"]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", 7), ["", "toto","ppo","","lolo","momo", "eorro"]);

        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", -1), ["", "toto","ppo","","lolo","momo" ]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", -3), ["", "toto","ppo",""]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", -7), [ ]);
        $this->assertEquals($str->explodeEx(array("/", "\\"), "/toto\ppo//lolo/momo\\eorro", -17), [ ]);

    }

}