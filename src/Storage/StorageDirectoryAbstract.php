<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Psr\Http\Message\StreamInterface;
use Bidoch78\Bimi\Storage\StorageAbstract;

abstract class StorageDirectoryAbstract extends StorageAbstract implements \SeekableIterator, \Countable {

    private ?array $_list = null;
    private int $_position = 0;
    private bool $_scanned = false;

    protected abstract function scan(): void;

    private function _runScan(): void {
        if ($this->_scanned) return;
        $this->_scanned = true;
        $this->scan();
    }

    public function reset(): void {
        parent::reset();
        $this->_scanned = false;
    }
    
    protected function addItem(StorageAbstract $item) {
        if (!$this->_list) $this->_list = [];
        $this->_list[] = $item;
    }

    public function count(): int {
        $this->_runScan();
        return ($this->_list) ? count($this->_list) : 0;    
    }

    public function seek(int $offset): void {
        $this->_runScan();
        if (!$this->_list || !isset($this->_list[$offset])) throw new \OutOfBoundsException("invalid seek position ($offset)");
        $this->_position = $offset;               
    }

    public function current(): StorageAbstract {   
        $this->_runScan();
        //if (!$this->_list) throw new OutOfBoundsException("run scan before to access");
        return $this->_list[$this->_position];
    }

    public function key(): mixed { return $this->_position; }
    public function next(): void { $this->_position++; }
    public function rewind(): void { $this->_position = 0; }
    public function valid(): bool { $this->_runScan(); return $this->_list && isset($this->_list[$this->_position]); }
    
}