<?php
namespace Cs\Router\Util;

use Symfony\Component\Filesystem\Filesystem;
use \Exception;

Abstract Class Assert {

    public static function isNumber($number, $message): Void {
        self::isEmpty($number, $message);
        if (preg_match('/[^0-9]/', $number)) {
            throw new Exception($message);
        }
    }

    public static function isArray($array, $message): Void {
        if (is_array($array) === false) {
            throw new Exception($message);
        }
    }

    public static function isHashArray($array, $message): Void {
        self::isArray($array, $message);
        if (array_keys($array) === range(0, count($array) - 1)) {
            throw new Exception($message);
        }
    }

    public static function isString($str, $message): Void {
        if (is_string($str) === false) {
            throw new Exception($message);
        }
    }

    public static function arrayNotEmpty($array, $message): Void {
        self::isArray($array, $message);
        if (count($array) === 0) {
            throw new Exception($message);
        }
    }

    public static function arrayKeyExists($key, $array, $message): Void {
        self::isArray($array, $message);
        if (array_key_exists($key, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function arrayIsEmpty($array, $message): Void {
        self::isArray($array, $message);
        if (count($array) > 0) {
            throw new Exception($message);
        }
    }

    public static function notNull($data, $message): Void {
        if (is_null($data) === true) {
            throw new Exception($message);
        }
    }

    public static function isEmpty($data, $message): Void {
        self::notNull($data, $message);
        self::isString($data, $message);
        if (strlen($data) === 0) {
            throw new Exception($message);
        }
    }

    public static function isJson($data, $message): Void {
        if (is_null($data) === true) {
            throw new Exception($message);
        }

        json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception($message);
        }
    }

    public static function isFolderExist($path, $message): Void {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($path) === false) {
            throw new Exception($message);
        }
    }

    public static function isArrayKeyExist($key, $array, $message): Void {
        if (array_key_exists($key, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function isArrayValueExist($value, $array, $message): Void {
        if (array_key_exists($value, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function inArray($value, $array, $message): Void {
        if (in_array($value, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function isBool($value, $message): Void {
        if (is_bool($value) === false) {
            throw new Exception($message);
        }
    }

    public function hasMethod($class, $method, $message): Void {
        if (method_exists($class, $method) === false) {
            throw new Exception($message);
        }
    }

    public function isCallable($class, $method, $message): Void {
        if (is_callable([$class, $method]) === false) {
            throw new Exception($message);
        }
    }
}
