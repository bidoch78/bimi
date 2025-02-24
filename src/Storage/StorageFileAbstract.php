<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Psr\Http\Message\StreamInterface;
use Bidoch78\Bimi\Storage\StorageAbstract;

abstract class StorageFileAbstract extends StorageAbstract {

    public abstract function getStream(): StreamInterface;

    public abstract function getContent(): string|bool;

    /* default behavoir if exists override otherwise define option [ 'appendifexist' => true ] */
    public abstract function putContent(string $content, array $options = null): bool;

}