<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Bidoch78\Bimi\Storage\StorageDirectoryAbstract;
use Bidoch78\Bimi\Storage\StorageFile;

class StorageDirectory extends StorageDirectoryAbstract {

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

        $this->_exist = is_dir($this->_path);
        
        if (!$this->_exist) {
            $this->reset();            
            $this->_error = self::ERROR_NOTEXISTS;
            return;
        }

        $this->_info = pathinfo(realpath($this->_path));

        parent::loadStatistics();
        
    }

    public function getName(): ?string {
        return (!$this->_error) ? $this->_info["filename"] : null;
    }
    
    public function getExtension(): ?string {
        return null;
    }

    public function getDirName(): ?string {
        return (!$this->_error) ? $this->_info["dirname"] : null;
    }

    public function getBaseName(): ?string {
        return (!$this->_error) ? $this->_info["basename"] : null;
    }

    public function isDir(): bool {
        return $this->_exist;
    }

    public function isFile(): bool {
        return false;
    }
    
    private function normalizePath(): string {
        $dir = $this->getPath();
        $lastChar = $dir[strlen($dir)-1];
        if (!($lastChar == "/" || $lastChar == "\\")) $dir .= DIRECTORY_SEPARATOR;
        return $dir;
    }

    protected function scan(): void {

        if (!$this->_exist) return;

        $dir = $this->normalizePath();
        foreach(scandir($dir) as $i) {
            if ($i == "." || $i == "..") continue;
            $item = $dir . $i;
            if (is_file($item)) $this->addItem(new StorageDirectory($item));
            else $this->addItem(new StorageFile($item));
        }

    }

    public function create(array $options = null): StorageInterface {
       
        

    }

    public function rename(string $name, array $options = null): bool {

    }

    public function delete(array $options = null): bool {

    }

    public function copy(StorageInterface $to, array $options = null): bool {
        
    }

    public function search(Search_Type $type, string $pattern): array {

        // if ($type = Search_Type::Shell)
        // if ($type = Search_Type::Regex)        

    }

    public function get(string $name): null|StorageAbstract {

        $item = realpath($this->normalizePath() . $name);
        if (!$item) return null;
        return (is_file($item)) ? new StorageFile($item) : new StorageDirectory($item);

    }

    public function addFolder(string $name, bool $onlyprepare = true): null|StorageAbstract {

    }

    public function addFile(string $name, bool $onlyprepare = true): null|StorageAbstract {

        
    }

}