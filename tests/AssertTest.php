<?php

use PHPUnit\Framework\TestCase;
use Cs\Router\Util\Assert;

class AssertTest extends TestCase {
    public function testStringIsNumberException() 
    {
        $this->expectException(Exception::class);
        Assert::isNumber('{test', 'invalid.input');
    }

    public function testEmptyIsNumberException() 
    {
        $this->expectException(Exception::class);
        Assert::isNumber('', 'invalid.input');
    }

    public function testArrayIsNumberException() 
    {
        $this->expectException(Exception::class);
        Assert::isNumber([], 'invalid.input');
    }

    public function testNumberIsNumber() 
    {
        Assert::isNumber('123', 'invalid.input');
        $this->assertTrue(TRUE);
    }

    public function testStringIsArrayException() 
    {
        $this->expectException(Exception::class);
        Assert::isArray('{test', 'invalid.input');
    }

    public function testEmptyIsArrayException() 
    {
        $this->expectException(Exception::class);
        Assert::isArray('', 'invalid.input');
    }
    
    public function testArrayIsArray() 
    {
        Assert::isArray([], 'invalid.input');
        $this->assertTrue(TRUE);
    }
    
    public function testNumberIsArrayException() 
    {
        $this->expectException(Exception::class);
        Assert::isArray('123', 'invalid.input');
    }
    
    public function testStringIsHashArrayException() 
    {
        $this->expectException(Exception::class);
        Assert::isHashArray('test', 'invalid.input');
    }

    public function testArrayIsHashArrayException() 
    {
        $this->expectException(Exception::class);
        Assert::isHashArray(['test'], 'invalid.input');
    }

    public function testHashArrayIsHashArray() 
    {
        Assert::isHashArray(['test'=>['test']], 'invalid.input');
        $this->assertTrue(TRUE);
    }
}