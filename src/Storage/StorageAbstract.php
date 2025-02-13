<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Storage;

use Psr\Http\Message\StreamInterface;

abstract class StorageAbstract implements StorageInterface {

    protected const ERROR_NO = 0;
    protected const ERROR_NOTEXISTS = 1;
    protected const ERROR_LSTAT = 2;

    protected ?array $_metadata = null;

    protected string $_path;

    protected int $_error = self::ERROR_NO;
    protected bool $_exist = false;    

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
        $this->_path = trim($path);
        if (!($options && isset($options['loadStatistics']) && $options['loadStatistics'] === true)) $this->loadStatistics();
    }

    public function loadStatistics(): void {

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
            case self::ERROR_NOTEXISTS: return ($this->isFile() ? "file" : "directory" ) + " not exists";
            case self::ERROR_LSTAT: return "Impossible to retrieve statistics";
        }
        return "";
    }

    public function reset():void {
        $this->_error = self::ERROR_NO;
        $this->_exist = false;
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
            return self::getUnixPermission($this->_mode);
        }
        return substr(sprintf('%o', $this->_mode), -4);
    }

    public function getSize(): ?int {
        return (!$this->_error) ? $this->_size : null;       
    }

    public function getPath(): string {
        return $this->_path;
    }

    public function isLink(): bool {
        return ($this->_exist) ? ($this->_link !== false) : false;
    }

    public function isLocal(): bool {
        return $this->_exist;
    }

    public static function getUnixPermission(int $perms): string {

        $info = "";

        switch ($perms & 0xF000) {
            case 0xC000: // socket
                $info = 's';
                break;
            case 0xA000: // symbolic link
                $info = 'l';
                break;
            case 0x8000: // regular
                $info = '-';
                break;
            case 0x6000: // block special
                $info = 'b';
                break;
            case 0x4000: // directory
                $info = 'd';
                break;
            case 0x2000: // character special
                $info = 'c';
                break;
            case 0x1000: // FIFO pipe
                $info = 'p';
                break;
            default: // unknown
                $info = 'u';
        }
        
        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
                    (($perms & 0x0800) ? 's' : 'x' ) :
                    (($perms & 0x0800) ? 'S' : '-'));
        
        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
                    (($perms & 0x0400) ? 's' : 'x' ) :
                    (($perms & 0x0400) ? 'S' : '-'));
        
        // World
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
                    (($perms & 0x0200) ? 't' : 'x' ) :
                    (($perms & 0x0200) ? 'T' : '-'));
        
        return $info;

    }

}