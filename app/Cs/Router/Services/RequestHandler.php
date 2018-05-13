<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cs\Router\Exception\InvalidRoutes;
use Cs\Router\Util\Assert;

class RequestHandler {
    protected $routes;
    protected $app;

    public function mapRoutes():void {
        $routes = $this->routes;
        $this->initializeRoutes($routes);
        foreach ($routes as $route) {
            $this->mapRequestToService($route);
        }
    }

    private function initializeRoutes($r) {
        $routes = $r;
        $map = [];
        $validRoutes = [];
        $this->hasValidRoutes($r);
        foreach ($routes as $route) {
            $map['url'] = $route['uri'];
            list($service, $func) = explode("->", $route['invoke']);
            $map['method'] = $route['method'];
            $map['service'] = $service;
            $map['func'] = $func;
            array_push($validRoutes, $map);
        }

        return $validRoutes;
    }

    private function hasValidRoutes($routing):void {
        $routes = $routing;
        foreach ($routes as $route) {
            Assert::isHashArray($route, 'each.route.must.have.array');
            $this->hasImpliesOperator($route['invoke']);
        }
    }

    private function hasImpliesOperator($r): void {
        Assert::notEmpty($r, 'invoke.param.not.found.in.route');

        if (!preg_match('/[a-zA-Z]{3,15}(->)[a-zA-Z]{5,}/', $r)) {
            throw new InvalidRoutes('invoke.route.is.invalid');
        }
    }

    private function mapRequestToService(Array $map, $slim) {
        $instance = $this;
        $callable = function (Request $request, Response $response, $args)
         use ($map, $instance) {
            $args = call_user_func([$instance, 'getInput'], $request, $args);
            $result = call_user_func([$instance->app[$map['service']], $map['func']], $args);
            return call_user_func([$instance, 'mapResponse'], $response, $result);
        };

        $pattern = $map['url'];
        $slim->map([$map['method']], $pattern, $callable);
    }

    public function getInput($request, $args) {
        $input = [];
        if ($request->getMethod() === "POST") {
            return $this->getPostInput($request);
        }

        if ($request->getMethod() === "GET") {
            return $this->getGetInput($request, $args);
        }

        throw new Exception("invalid.request.method");
    }

    private function getPostInput($request) {
        $input = $request->getParsedBody();
        $input = is_string($input) === true ? json_decode($input, true) : $input;
        return $input;
    }

    private function getGetInput($request, $args) {
        if (is_array($args) === true && count($args) > 0) {
            return $args;
        }

        return $request->getQueryParams();
    }

    private function mapResponse($response, $result) {
        return $this->responseHandler->setResponse($response, $result);
    }
}
