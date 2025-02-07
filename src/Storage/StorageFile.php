<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Bidoch78\Bimi\Storage\StorageFileAbstract;
use Bidoch78\Bimi\Storage\StorageFileStream;

use Psr\Http\Message\StreamInterface;

class StorageFile extends StorageFileAbstract {

    private const ERROR_NO = 0;
    private const ERROR_NOTEXISTS = 1;
    private const ERROR_LSTAT = 2;

    private string $_path;

    private int $_error = self::ERROR_NO;
    private bool $_exist = false;

    private ?array $_info = null;

    private ?int $_size = null;
    private ?int $_gid = null;
    private ?int $_uid = null;
    private ?int $_mtime = null;
    private ?int $_ctime = null;
    private ?int $_atime = null;
    private bool|string $_link = false;
    private ?int $_inode = null;
    private ?int $_mode = null;

    public function __construct(string $path, array $options = null) {
        $this->_path = $path;
        if (!($options && isset($options['loadStatistics']) && $options['loadStatistics'] === true)) $this->loadStatistics();
    }

    public function reset():void {
        $this->_error = self::ERROR_NO;
        $this->_exist = false;
        $this->_info = null;
        $this->_size = null;
        $this->_gid = null;
        $this->_uid = null;
        $this->_mtime = null;
        $this->_ctime = null;
        $this->_atime = null;
        $this->_link = false;
        $this->_inode = null;
        $this->_mode = null;
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

        $info = lstat($this->_path);
        if ($info === false) {
            $this->reset();            
            $this->_error = self::ERROR_LSTAT;
        }

        $this->_size = $info["size"];
        $this->_gid = $info["gid"];
        $this->_uid = $info["uid"];
        $this->_mtime = $info["mtime"];
        $this->_ctime = $info["ctime"];
        $this->_atime = $info["atime"];
        
        $this->_inode = $info["ino"];
        $this->_mode = $info["mode"];

        $this->_link = is_link($this->_path) ? readlink($this->_path) : false;
        
    }

    public function refresh(): void {
        $this->reset();
        $this->loadStatistics();
    }

    public function exists(): bool { return $this->_exist; }
    public function hasError(): bool { return $this->_error !== self::ERROR_NO; }
    public function getErrorCode(): int { return $this->_error; }
    public function getError(): string {
        switch($this->_error) {
            case self::ERROR_NOTEXISTS: return "file not exists";
            case self::ERROR_LSTAT: return "Impossible to retrieve statistics";
        }
        return "";
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

    public function getCreationTime(): ?\DateTime {
        if ($this->_error) return null;
        $date = new \DateTime();
        $date->setTimestamp($this->_ctime);
        return $date;       
    }

    public function getModifyTime(): ?\DateTime {
        if ($this->_error) return null;
        $date = new \DateTime();
        $date->setTimestamp($this->_mtime);
        return $date;
    }

    public function getAccessTime(): ?\DateTime {
        if ($this->_error) return null;
        $date = new \DateTime();
        $date->setTimestamp($this->_atime);
        return $date;       
    }    

    public function getGID(): ?int {
        return ($this->_exist) ? $this->_gid : null;
    }

    public function getUID(): ?int {
        return ($this->_exist) ? $this->_uid : null;
    }

    public function getPermission(int $format = PERMISSION_OCTAL): null|int|string {
        if (!$this->_exist) return null;
        if ($format == PERMISSION_STRING) {
            return StorageFileAbstract::getUnixPermission($this->_mode);
        }
        return substr(sprintf('%o', $this->_mode), -4);
    }

    public function getSize(): ?int {
        return (!$this->_error) ? $this->_size : null;       
    }

    public function getPath(): string {
        return $this->_path;
    }

    public function isDir(): bool {
        return false;
    }

    public function isFile(): bool {
        return $this->_exist;
    }
    
    public function isLink(): bool {
        return ($this->_exist) ? ($this->_link !== false) : false;
    }

    public function isLocal(): bool {
        return $this->_exist;
    }

    public function getStream(): StreamInterface {

        if (!$this->exists()) throw new \InvalidArgumentException('file `' + $this->_path + '` not exists');
        
        $file = fopen($this->_path, 'r');
        if ($file === false) throw new \InvalidArgumentException('file `' + $this->_path + '` not accessible');

        return new StorageFileStream($file, [ "size" => $this->getSize() ]);

    }

}