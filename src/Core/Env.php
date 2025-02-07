<?php

declare(strict_types=1);

namespace Bidoch78\Bimi\Core;

final class Env {

    private static ?array $_fileValues = null;
    private static $_filePath = null;

    private static array $_order = [ 'D', 'F', 'E' ];

    /**
     * Env 
     *  In priority :
     *      - check define
     *      - check in .env file
     *      - check environment variable
     */

    public static function setFilePath(string $path): void {
        self::$_filePath = $path;
    }

    public static function getDefine(string $name, string $default = null):null|string {
        return (defined($name)) ? constant($name) : null;
    }

    public static function readEnvFile(string $path):array {
        if (!is_file($path)) return [];
        $values = [];
        foreach(file($path, FILE_IGNORE_NEW_LINES) as $line) {
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
            if (!self::$_filePath) return null;
            self::$_fileValues = self::readEnvFile(self::$_filePath);
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