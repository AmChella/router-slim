<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cs\Router\Exception\InvalidRoute;
use Cs\Router\Exception\InvalidMethodType;
use Cs\Router\Util\Assert;
use Cs\Router\Traits\HttpPayload;
use Psr\Http\Server\RequestHandlerInterface as RHandler;
use \Exception;

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
            $mapping['url'] = $route['url'];
            list($service, $func) = explode("->", $route['invoke']);
            $mapping['method'] = \strtoupper($route['method']);
            $mapping['service'] = $service;
            $mapping['func'] = $func;
            $mapping['middlewares'] = $route['routeBeforeInvoke'] ?? [];
            $mapping['returnMode'] = $route['return'] ?? null;
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
        $this->isArrayKeyExist('url', $route, 'uri.not.found');
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
            try {
                $args = \call_user_func([$instance, 'getPayload'], $request, $args);
                $returnMode = $instance->getReturnMode($args['params'], $map['returnMode']);
                $rbiResult = $instance->routeBeforeInvoker($map['middlewares'], $args);
                $result = \call_user_func(
                    [$instance->getClass($map['service']), $map['func']], $rbiResult
                );
                return \call_user_func(
                    [$instance->responseHandler, 'setResponse'],
                    $response, $result, $returnMode
                );
            }
            catch(Exception $e) {
                $result['statusCode'] = 500;
                if ($e->getCode()) {
                    $result['statusCode'] = $e->getCode();
                }
                $result['message'] = 'something.went.wrong';
                if ($instance->debug === true) {
                    $result['message'] = $e->getTraceAsString();
                }

                return \call_user_func(
                    [$instance->responseHandler, 'setResponse'],
                    $response, $result, $returnMode
                );
            }
        };


        $path = $map['url'];
        $this->app->map([$map['method']], $path, $callable);
    }

    /**
     * routeBeforeInvoker
     *
     * @param  mixed $middleWares
     * @param  mixed $args
     * @return Array
     */
    private function routeBeforeInvoker(
        Array $middleWares, Array $args
    ): Array {
        $payload = $args;
        $result = [];
        foreach($middleWares as $middleware) {
            list($service, $func) = explode("->", $middleware);
            $this->checkRouteHasValidCallback($service, $func);
            $result[$func] = call_user_func_array(
                [$this->getClass($service), $func], [$args]
            );
        }

        return array_merge($payload, $result);
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
