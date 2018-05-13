<?php
namespace Cs\Router\Util;

use Symfony\Component\Filesystem\Filesystem;
use \Exception;

class Assert {

    public static function isNumber($number, $message) {
        if (preg_match('/[^0-9]/', $number)) {
            throw new Exception($message);
        }
    }

    public static function isArray($array, $message) {
        if (!is_array($array)) {
            throw new Exception($message);
        }
    }

    public static function isHashArray($array, $message) {
        self::isArray($array, $message);
        if (array_keys($array) === range(0, count($array) - 1)) {
            throw new Exception($message);
        }
    }

    public static function isString($str, $message) {
        if (!is_string($str)) {
            throw new Exception($message);
        }
    }

    public static function arrayNotEmpty($array, $message) {
        self::isArray($array, $message);
        if (count($array) === 0) {
            throw new Exception($message);
        }
    }

    public static function arrayKeyExists($array, $key, $message) {
        self::isArray($array, $message);
        if (array_key_exists($key, $array)) {
            return true;
        }

        return false;
    }

    public static function arrayIsEmpty($array, $message) {
        self::isArray($array, $message);
        if (count($array) > 0) {
            throw new Exception($message);
        }
    }

    public static function notNull($data, $message) {
        if (is_null($data)) {
            throw new Exception($message);
        }
    }

    public static function notEmpty($data, $message) {
        self::notNull($data, $message);
        self::isString($data, $message);
        if (strlen($data) === 0) {
            throw new Exception($message);
        }
    }

    public static function isEmpty($data, $message) {
        self::isString($data, $message);
        if (strlen($data) === 0) {
            throw new Exception($message);
        }
    }

    public static function isJson($data, $message) {
        if (is_null($data) || is_null(json_decode($data))) {
            throw new Exception($message);
        }
    }

    public static function isFolderExist($path, $message) {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($path) === false) {
            throw new Exception($message);
        }
    }

    public static function isArrayKeyExist($value, $array, $message) {
        if (array_key_exists($value, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function isArrayValueExist($value, $array, $message) {
        if (array_key_exists($value, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function inArray($value, $array, $message) {
        if (in_array($value, $array) === false) {
            throw new Exception($message);
        }
    }

    public static function isBool($value, $message) {
        if (is_bool($value) === false) {
            throw new Exception($message);
        }
    }

    public static function hasInput($value, $message) {
        $data = json_decode($value, true);
        if (isset($data['input']['html']) === false) {
            throw new Exception($message);
        }
    }

    public static function hasOutput($value, $message) {
        $data = json_decode($value, true);
        if (isset($data['remote_storage']) === false) {
            throw new Exception($message);
        }
    }

    public static function throwOnInValid($value, $message) {
        if ($value === false) {
            throw new Exception($message);
        }
    }
    public static function hasStage($value, $message) {
        $data = json_decode($value, true);
        if (isset($data['stage']) === false) {
            throw new Exception($message);
        }
    }

    public static function hasRemoteStorage($value, $message) {
        if (is_array($value) === false  
            && count($value) === 0) {
            throw new Exception($message);
        }
    }
}
