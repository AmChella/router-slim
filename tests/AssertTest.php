<?php

use PHPUnit\Framework\TestCase;
use Cs\Router\Util\Assert;

class AssertTest extends TestCase {
    public function testStringIsNumberException() {
        $this->expectException(Exception::class);
        Assert::isNumber('{test', 'invalid.input');
    }

    public function testEmptyIsNumberException() {
        $this->expectException(Exception::class);
        Assert::isNumber('', 'invalid.input');
    }

    public function testArrayIsNumberException() {
        $this->expectException(Exception::class);
        Assert::isNumber([], 'invalid.input');
    }

    public function testNumberIsNumber() {
        $this->expectException(Exception::class);
        Assert::isNumber('123', 'invalid.input');
    }

    public function testStringIsArrayException() {
        $this->expectException(Exception::class);
        Assert::isArray('{test', 'invalid.input');
    }

    public function testEmptyIsArrayException() {
        $this->expectException(Exception::class);
        Assert::isArray('', 'invalid.input');
    }
    
    public function testArrayIsArray() {
        Assert::isArray([], 'invalid.input');
        $this->assertTrue(TRUE);
    }
    
    public function testNumberIsArrayException() {
        $this->expectException(Exception::class);
        Assert::isArray('123', 'invalid.input');
    }
    
    public function testStringIsHashArrayException() {
        $this->expectException(Exception::class);
        Assert::isHashArray('test', 'invalid.input');
    }

    public function testArrayIsHashArrayException() {
        $this->expectException(Exception::class);
        Assert::isHashArray(['test'], 'invalid.input');
    }
    
    public function testHashArrayIsHashArray() {
        Assert::isHashArray(['test'=>['test']], 'invalid.input');
        $this->assertTrue(TRUE);
    }
    
    public function testStringIsString() {
        Assert::isString('test', 'invalid.input');
        $this->assertTrue(TRUE);
    }
    
    public function testNumberIsString() {
        $this->expectException(Exception::class);
        Assert::isString(10, 'invalid.input');
    }

    public function testArrayIsString() {
        $this->expectException(Exception::class);
        Assert::isString([], 'invalid.input');
    }
    
    public function testEmptyArrayNotEmpty() {
        $this->expectException(Exception::class);
        Assert::arrayNotEmpty([], 'invalid.input');
    }

    public function testStringArrayNotEmpty() {
        $this->expectException(Exception::class);
        Assert::arrayNotEmpty('test', 'invalid.input');
    }
    
    public function testNotEmptyArrayNotEmpty() {
        Assert::arrayNotEmpty(['test'], 'invalid.input');
        $this->assertTrue(TRUE);
    }
    
    public function testKeyExistArrayKeyExists() {
        Assert::arrayKeyExists('test', ['test' => 'test'], 'invalid.input');
        $this->assertTrue(TRUE);
    }
    
    public function testKeyExistsSuccess() {
        $this->expectException(Exception::class);
        Assert::arrayKeyExists('tests', ['test' => 'test'], 'invalid.input');
    }

    public function testKeyExistsWithString() {
        $this->expectException(Exception::class);
        Assert::arrayKeyExists('tests', 'test', 'invalid.input');
    }

    public function testArrayIsEmptyWithString() {
        $this->expectException(Exception::class);
        Assert::arrayIsEmpty('tests', 'invalid.input');
    }

    public function testIsEmptyWithString() {
        Assert::isEmpty('tests', 'invalid.input');
        $this->assertTrue(TRUE);
    }

    public function testIsEmptyWithNumber() {

        // $this->expectException(Exception::class);
        Assert::isEmpty(123, 'invalid.input');
        $this->assertTrue(TRUE);
    }

    public function testIsJsonWithString() {
        $this->expectException(Exception::class);
        Assert::isJson('tests', 'invalid.input');
    }

    public function testIsJsonWithJson() {
        Assert::isJson('{"test":"test"}', 'invalid.input');
        $this->assertTrue(TRUE);
    }
}