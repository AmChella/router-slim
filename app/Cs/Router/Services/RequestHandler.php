<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cs\Router\Exception\InvalidRoute;
use Cs\Router\Exception\InvalidMethodType;
use Cs\Router\Util\Assert;
use Cs\Router\Traits\HttpPayload;

Class RequestHandler extends Assert {
    use HttpPayload;
    protected $app;
    protected $routes;
    protected $containers;
    protected $responseHandler;

    /**
     * assignRoutesToService
     *
     * @param  mixed $routes
     *
     * @return Void
     */
    protected function routeToService(Array $routes): Void {
        $mapping = [];
        foreach ($routes as $route) {
            $this->validateRoute($route);
            $mapping['url'] = $route['uri'];
            list($service, $func) = explode("->", $route['invoke']);
            $mapping['method'] = $route['method'];
            $mapping['service'] = $service;
            $mapping['func'] = $func;
            $mapping['return'] = $route['return'] ?? 'json';
            $this->mapCallback($mapping);
        }
    }

    /**
     * validateRoute
     *
     * @param  mixed $route
     *
     * @return Void
     */
    public function validateRoute(Array $route): Void {
        $this->isHashArray($route, 'route.is.not.an.array');
        $this->isArrayKeyExist('invoke', $route, 'invoke.key.not.found');
        $this->isArrayKeyExist('uri', $route, 'uri.not.found');
        $this->isArrayKeyExist('method', $route, 'method.not.found');
        if (!preg_match('/[a-zA-Z]{3,}(->)[a-zA-Z]{5,}/', $route['invoke'])) {
            throw new InvalidRoute('invoke.pattern.is.invalid');
        }

        list($service, $func) = explode("->", $route['invoke']);
        $this->checkRouteHasValidCallback($service, $func);
    }

    /**
     * validateServiceHasValidCallback
     *
     * @param  mixed $container
     * @param  mixed $class
     * @param  mixed $method
     *
     * @return void
     */
    public function checkRouteHasValidCallback(
        String $class, String $method
    ): Void {
        $msg = sprintf('func.%s.not.found', $method);
        $this->hasMethod($this->getClass($class), $method, $msg);
        $msg = sprintf('func.%s.not.callable', $method);
        $this->isMethodCallable($this->getClass($class), $method, $msg);
    }

    /**
     * assignCallback
     *
     * @param  mixed $map
     *
     * @return Void
     */
    private function mapCallback(Array $map): Void {
        $instance = $this;
        $callable = function (
            Request $request, Response $response, $args
        ) use ($map, $instance) {
            $args = \call_user_func([$instance, 'getPayload'], $request, $args);
            $result = \call_user_func(
                [$instance->getClass($map['service']), $map['func']], $args
            );

            return \call_user_func(
                [$instance->responseHandler, 'setResponse'],
                $response, $result, $map['return'] ?? 'json'
            );
        };

        $pattern = $map['url'];
        $this->app->map([$map['method']], $pattern, $callable);
    }

    /**
     * getClass
     *
     * @param  mixed $class
     *
     * @return Object
     */
    private function getClass(String $class): Object {
        if (\method_exists($this->containers, 'get') === true) {
            return $this->containers->get($class);
        }

        return $this->containers[$class];
    }
}
