<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Psr\Http\Message\StreamInterface;

abstract class StorageDirectoryAbstract implements \SeekableIterator, \Countable, StorageInterface {

    public abstract function scan(): bool;    
    
    

}