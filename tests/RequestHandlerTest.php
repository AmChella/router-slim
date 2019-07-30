<?php

use PHPUnit\Framework\TestCase;
use Cs\Router\Util\Assert;
use Cs\Router\Services\RequestHandler;
use Cs\Router\Exception\InvalidRoute;

class RequestHanderTest extends TestCase {
    public function setUp() {
        $this->mcValidateRoute = $this->getMockBuilder(RequestHandler::Class)
        ->setMethods(['validateServiceHasValidCallback'])
        ->getMock();
        $this->service = $this->getMockBuilder('welcomeService')
        ->setMethods(['welcome'])
        ->getMock();
    }


    public function testValidateRouteExceptionOnRouteIsNotAnArray() {
        $this->expectException(TypeError::Class);
        $this->mcValidateRoute->validateRoute('');
    }

    public function testValidateRouteExceptionOnRouteIsAnEmptyArray() {
        $this->expectException(Exception::Class);
        $this->expectExceptionMessage('invoke.key.not.found');
        $this->mcValidateRoute->validateRoute([]);
    }

    public function testValidateRouteExceptionOnRouteHasNoInvokeKey() {
        $this->expectException(Exception::Class);
        $this->expectExceptionMessage('invoke.key.not.found');
        $this->mcValidateRoute->validateRoute(['uri' => '']);
    }

    public function testValidateRouteExceptionOnRouteHasNoUriKey() {
        $this->expectException(Exception::Class);
        $this->expectExceptionMessage('uri.not.found');
        $this->mcValidateRoute->validateRoute(['invoke' => '']);
    }

    public function testValidateRouteExceptionOnRouteHasNoMethodKey() {
        $this->expectException(Exception::Class);
        $this->expectExceptionMessage('method.not.found');
        $this->mcValidateRoute->validateRoute(['invoke' => '', 'uri' => '']);
    }

    public function testValidateRouteExceptionOnRouteHasInvalidUri() {
        $this->expectException(InvalidRoute::Class);
        $this->expectExceptionMessage('route.uri.invalid');
        $this->mcValidateRoute->validateRoute([
            'invoke' => '', 'uri' => '', 'method' => ''
        ]);
    }

    public function testValidateRouteExceptionOnRouteHasInvalidInvokeType2() {
        $this->expectException(InvalidRoute::Class);
        $this->expectExceptionMessage('route.uri.invalid');
        $this->mcValidateRoute->validateRoute([
            'uri' => '', 'invoke' => 'test', 'method' => ''
        ]);
    }

    public function testValidateRouteExceptionOnRouteHasInvalidInvokeWithLess2CharClass() {
        $this->expectException(InvalidRoute::Class);
        $this->expectExceptionMessage('route.uri.invalid');
        $this->mcValidateRoute->validateRoute([
            'uri' => '', 'invoke' => 'te->test', 'method' => ''
        ]);
    }

    public function testValidateRouteExceptionOnRouteHasInvalidInvokeWithLess5CharMethodName() {
        $this->expectException(InvalidRoute::Class);
        $this->expectExceptionMessage('route.uri.invalid');
        $this->mcValidateRoute->validateRoute([
            'uri' => '', 'invoke' => 'tet->test', 'method' => ''
        ]);
    }

    public function testValidateRouteExceptionOnRouteHasInvalidInvokeClassName(
    ) {
        $this->expectException(InvalidRoute::Class);
        $this->expectExceptionMessage('route.uri.invalid');
        $this->mcValidateRoute->validateRoute([
            'uri' => '', 'invoke' => '2et->testf', 'method' => ''
        ]);
    }

    public function testValidateRouteExceptionOnRouteHasInvalidInvokeMethodName(
    ) {
        $this->expectException(InvalidRoute::Class);
        $this->expectExceptionMessage('route.uri.invalid');
        $this->mcValidateRoute->validateRoute([
            'uri' => '', 'invoke' => 'eet->tes3f', 'method' => ''
        ]);
    }

    public function testValidateRouteSuccess() {
        $this->mcValidateRoute->expects($this->once())
        ->method('validateServiceHasValidCallback')->with(
            null,
            $this->stringContains('eet'), 
            $this->stringContains('testf')
        );
        $this->mcValidateRoute->validateRoute([
            'uri' => '', 'invoke' => 'eet->testf', 'method' => ''
        ]);
    }

    public function testValidateServiceHasValidCallbackExceptionNoMethod() {
        $validateCallback = $this->getMockBuilder(RequestHandler::Class)
        ->setMethods(['assignCallback'])->getMock();
        $this->expectException(Exception::Class);
        $this->expectExceptionMessage('func.super.not.found');
        $container['welcomeService'] = '';
        $validateCallback->validateServiceHasValidCallback(
            $container, 'welcomeService', 'super'
        );
    }

    public function testValidateServiceHasValidCallbackSuccess() {
        $validateCallback = $this->getMockBuilder(RequestHandler::Class)
        ->setMethods(['assignCallback'])->getMock();
        $container['welcomeService'] = $this->service;
        $ret = $validateCallback->validateServiceHasValidCallback(
            $container, 'welcomeService', 'welcome'
        );
        $this->assertEquals(null, $ret);
    }
}   