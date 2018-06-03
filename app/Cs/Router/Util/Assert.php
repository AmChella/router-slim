<?php
namespace Cs\Router\Util;

use Symfony\Component\Filesystem\Filesystem;
use \Exception;

abstract class Assert {

    public static function isNumber($number, $message): void 
    {
        self::isEmpty($number, $message);
        if (preg_match('/[^0-9]/', $number)) {
            throw new Exception($message);
        }
    }

    public static function isArray($array, $message): void 
    {
        if (is_array($array) === false) {
            throw new Exception($message);
        }
    }

    public static function isHashArray($array, $message): void 
    {
        self::isArray($array, $message);
        if (array_keys($array) === range(0, count($array) - 1)) {
            throw new Exception($message);
        }
    }

    public static function isString($str, $message): void 
    {
        if (is_string($str) === false) {
            throw new Exception($message);
        }
    }

    public static function arrayNotEmpty($array, $message): void 
    {
        self::isArray($array, $message);
        if (count($array) === 0) {
            throw new Exception($message);
        }
    }
    
    public static function arrayKeyExists($array, $key, $message): void 
    {
        self::isArray($array, $message);
        if (array_key_exists($key, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function arrayIsEmpty($array, $message): void 
    {
        self::isArray($array, $message);
        if (count($array) > 0) {
            throw new Exception($message);
        }
    }

    public static function notNull($data, $message): void 
    {
        if (is_null($data) === false) {
            throw new Exception($message);
        }
    }

    public static function notEmpty($data, $message): void 
    {
        self::notNull($data, $message);
        self::isString($data, $message);
        if (strlen($data) === 0) {
            throw new Exception($message);
        }
    }

    public static function isEmpty($data, $message): void 
    {
        self::isString($data, $message);
        if (strlen($data) === 0) {
            throw new Exception($message);
        }
    }

    public static function isJson($data, $message): void 
    {
        if (is_null($data) === true || is_null(json_decode($data)) === true) {
            throw new Exception($message);
        }
    }

    public static function isFolderExist($path, $message): void 
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($path) === false) {
            throw new Exception($message);
        }
    }

    public static function isArrayKeyExist($key, $array, $message): void 
    {
        if (array_key_exists($key, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function isArrayValueExist($value, $array, $message): void 
    {
        if (array_key_exists($value, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function inArray($value, $array, $message): void 
    {
        if (in_array($value, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function isBool($value, $message): void 
    {
        if (is_bool($value) === false) {
            throw new Exception($message);
        }
    }

    public function hasMethod($class, $method, $message): void 
    {
        if (method_exists($class, $method) === false) {
            throw new Exception($message);
        } 
    }
    
    public function isCallable($class, $method, $message): void 
    {
        if (is_callable([$class, $method]) === false) {
            throw new Exception($message);
        }
    }
}
