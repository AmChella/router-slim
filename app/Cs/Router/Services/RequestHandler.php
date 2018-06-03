<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cs\Router\Exception\RouteException;
use Cs\Router\Util\Assert;

class RequestHandler {
    protected $routes;
    protected $app;
    protected $containers;

    public function mapRoutes():void 
    {
        $routes = $this->routes;
        $validRoutes = $this->initializeRoutes($routes);
        foreach ($validRoutes as $route) {
            $this->mapRequestToService($route);
        }
    }

    private function initializeRoutes($routes): array 
    {
        $map = [];
        $validRoutes = [];
        $this->hasValidRoutes($routes);
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

    private function hasValidRoutes($routing): void 
    {
        $routes = $routing;
        foreach ($routes as $route) {
            Assert::isHashArray($route, 'each.route.must.have.array');
            $this->hasImpliesOperator($route['invoke']);
        }
    }

    private function hasImpliesOperator($route): void 
    {
        Assert::notEmpty($route, 'invoke.param.not.found.in.route');
        if (!preg_match('/[a-zA-Z]{3,15}(->)[a-zA-Z]{5,}/', $route)) {
            throw new RouteException('invoke.route.is.invalid');
        }
    }

    private function mapRequestToService(Array $map): void 
    {
        $instance = $this;
        $callable = function (
            Request $request, Response $response, $args
        ) use ($map, $instance) {
            $args = call_user_func([$instance, 'getInput'], $request, $args);
            $result = call_user_func(
                [$instance->containers[$map['service']], $map['func']], $args
            );

            return call_user_func([$instance, 'mapResponse'], $response, $result);
        };

        $pattern = $map['url'];
        $this->app->map([$map['method']], $pattern, $callable);
    }

    public function getInput($request, $args): string 
    {
        if ($request->getMethod() === "POST") {
            return $this->getPostInput($request);
        }

        if ($request->getMethod() === "GET") {
            return $this->getGetInput($request, $args);
        }

        throw new Exception("invalid.request.method");
    }

    private function getPostInput($request): string 
    {
        $input = $request->getParsedBody();
        $input = is_string($input) === true ? json_decode($input, true) : $input;
        return $input;
    }

    private function getGetInput($request, $args): string 
    {
        if (is_array($args) === true && count($args) > 0) {
            return $args;
        }

        return $request->getQueryParams();
    }

    private function mapResponse($response, $result): string 
    {
        $message = $result ?? 'found.no.response';
        $status = $result['status'] ?? 'success';
        $info = [
            'status' => $status,
            'message' => $message
        ];

        return $response->withJson($info);
    }
}
