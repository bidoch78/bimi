<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Bidoch78\Bimi\Storage\StorageFileAbstract;
use Bidoch78\Bimi\Storage\StorageFileStream;
use Bidoch78\Bimi\Storage\StorageInterface;
use Bidoch78\Bimi\Storage\StorageAbstract;

use Psr\Http\Message\StreamInterface;

class StorageFile extends StorageFileAbstract {

    private ?array $_info = null;

    public function __construct(string $path, array $options = null) {
        parent::__construct($path, $options);
    }

    public function reset():void {
        parent::reset();
        $this->_info = null;
    }

    public function loadStatistics(): void {

        if ($this->_exist) return;

        $this->_exist = is_file($this->_path);

        if (!$this->_exist) {
            $this->reset();            
            $this->_error = self::ERROR_NOTEXISTS;
            return;
        }

        $this->_info = pathinfo($this->_path);

        parent::loadStatistics();
        
    }

    public function getName(): ?string {
        return (!$this->_error) ? $this->_info["filename"] : null;
    }
    
    public function getExtension(): ?string {
        return (!$this->_error) ? $this->_info["extension"] : null;
    }

    public function getDirName(): ?string {
        return (!$this->_error) ? $this->_info["dirname"] : null;
    }

    public function getBaseName(): ?string {
        return (!$this->_error) ? $this->_info["basename"] : null;
    }

    public function isDir(): bool {
        return false;
    }

    public function isFile(): bool {
        return $this->_exist;
    }

    public function getStream(): StreamInterface {

        if (!$this->exists()) throw new \InvalidArgumentException('file `' + $this->_path + '` not exists');
        
        $file = fopen($this->_path, 'r');
        if ($file === false) throw new \InvalidArgumentException('file `' + $this->_path + '` not accessible');

        return new StorageFileStream($file, [ "size" => $this->getSize() ]);

    }

    public function create(array $options = null): StorageInterface {

        if (!$this->_exist) {
            $handle = fopen($this->_path, "w");
            fclose($handle);
            $this->refresh();
        }

        return $this;

    }

    public function rename(string $name, array $options = null): bool {

        if (!$this->_exist) return false;

        $newPath = $this->getDirName() . DIRECTORY_SEPARATOR . $name;
        $return = rename($this->_path, $newPath);
        
        if (!$return) return false;

        $this->_path = $newPath;
        $this->refresh();

        return true;

    }

    public function delete(array $options = null): bool {

        if (!$this->_exist) return true;

        $return = unlink($this->_path);
        if ($return) $this->refresh();
        
        return $return;

    }

    public function copy(StorageInterface $to, array $options = null): bool {
        
        if (!$this->_exist) throw new \InvalidArgumentException('file `' + $this->_path + '` not exists');

        if (($to instanceof StorageAbstract)) {
            return copy($this->_path, $to->_path);
        }
        
        // Use stream
        throw \RuntimeException("stream copy need to be implemented");

    }

}