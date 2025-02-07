<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Core;

abstract class AbstractCore {

    private ?AbstractCore $_singleton = null;

    public static function getInstance():AbstractCore {
        return $this->$_singleton;
    }

    public static function register(AbstractCore $core):void {
        $this->$_singleton = $core;
    }

}