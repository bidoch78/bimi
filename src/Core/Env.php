<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Core;
use Bidoch78\Bimi\Storage\StorageAbstract;

final class Env {

    private static ?array $_fileValues = null;
    private static ?StorageAbstract $_file = null;

    private static array $_order = [ 'D', 'F', 'E' ];

    /**
     * Env 
     *  In priority :
     *      - check define
     *      - check in .env file
     *      - check environment variable
     */

    public static function setFilePath(StorageAbstract $file): void {
        self::$_file = $file;
    }

    public static function getDefine(string $name, string $default = null):null|string {
        return (defined($name)) ? constant($name) : null;
    }

    public static function readEnvFile(StorageAbstract $file):array {
        if (!self::$_file->exists()) return [];
        $values = [];
        $lines = preg_split('/\R+/', self::$_file->getContent(), 0, PREG_SPLIT_NO_EMPTY);
        foreach($lines as $line) {
            $sep = strpos($line, "=");
            if ($sep !== false) {
                $key = trim(substr($line, 0, $sep));
                if (!$key) continue;
                if ($key[0] == "#") continue;
                $value = trim(substr($line, $sep + 1));
                if ($value && $value[0] === "\"") {
                    $value = substr($value, 1);
                    $len = strlen($value);
                    if ($value[$len-1] === "\"") $value = substr($value, 0, $len - 1);
                }
                $values[$key]=$value;
            }
        }
        return $values;
    }

    public static function getFile(string $name, string $default = null):null|string {
        if (!self::$_fileValues) {
            if (!self::$_file) return null;
            self::$_fileValues = self::readEnvFile(self::$_file);
        }
        return isset(self::$_fileValues[$name]) ? self::$_fileValues[$name] : null;
    }

    public static function getEnv(string $name, string $default = null):null|string {
       return getenv($name) ? getenv($name): null;
    }    

    public static function get(string $name, string $default = null):mixed {

        $value = null;
        foreach(self::$_order as $type) {
            switch($type) {
                case 'D': 
                    $value = self::getDefine($name);
                    if ($value) return $value;
                    break;
                case 'F':
                    $value = self::getFile($name);
                    if ($value) return $value;
                    break;                    
                case 'E':
                    $value = self::getEnv($name);
                    if ($value) return $value;
                    break;                    
                default:
                    throw new Exception("Env type " . $type . " unknown");
            }
        }

        return $default;

    }

}