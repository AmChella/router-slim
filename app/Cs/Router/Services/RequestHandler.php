<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cs\Router\Exception\InvalidRoute;
use Cs\Router\Exception\InvalidMethodType;
use Cs\Router\Util\Assert;

class RequestHandler extends Assert {
    protected $routes;
    protected $app;
    protected $containers;
    protected $responseHandler;

    /**
     * assignRoutesToService
     *
     * @param  mixed $routes
     *
     * @return Void
     */
    protected function assignRoutesToService(Array $routes): Void {
        $map = [];
        foreach ($routes as $route) {
            $this->validateRoute($route);
            $map['url'] = $route['uri'];
            list($service, $func) = explode("->", $route['invoke']);
            $map['method'] = $route['method'];
            $map['service'] = $service;
            $map['func'] = $func;
            $map['return'] = $route['return'] ?? 'json';
            $this->assignCallback($map);
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
        $this->isHashArray($route, 'routes.not.an.array');
        $this->isArrayKeyExist('invoke', $route, 'invoke.key.not.found');
        $this->isArrayKeyExist('uri', $route, 'uri.not.found');
        $this->isArrayKeyExist('method', $route, 'method.not.found');
        if (!preg_match('/[a-zA-Z]{3,15}(->)[a-zA-Z]{5,}/', $route['invoke'])) {
            throw new InvalidRoute('route.uri.invalid');
        }

        list($service, $func) = explode("->", $route['invoke']);
        $this->validateServiceHasValidCallback(
            $this->containers, $service, $func
        );
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
    public function validateServiceHasValidCallback(
        $container, String $class, String $method
    ): Void {
        $msg = sprintf('func.%s.not.found', $method);
        $this->hasMethod($container[$class], $method, $msg);
        $msg = sprintf('func.%s.not.callable', $method);
        $this->isCallable($container[$class], $method, $msg);
    }

    /**
     * assignCallback
     *
     * @param  mixed $map
     *
     * @return Void
     */
    private function assignCallback(Array $map): Void {
        $instance = $this;
        $callable = function (
            Request $request, Response $response, $args
        ) use ($map, $instance) {
            $args = call_user_func([$instance, 'getPayload'], $request, $args);
            $result = call_user_func(
                [$instance->containers[$map['service']], $map['func']], $args
            );

            return call_user_func(
                [$instance->responseHandler, 'setResponse'],
                $response, $result, $map['return'] ?? 'json'
            );
        };

        $pattern = $map['url'];
        $this->app->map([$map['method']], $pattern, $callable);
    }

    /**
     * getPostData
     *
     * @param  mixed $req
     *
     * @return Array
     */
    private function getPostData(Request $req): Array {
        $postData = [];
        $postData['data'] = $req->getParsedBody();
        $postData['headers'] = $this->getHeaders($req);
        if (count($req->getUploadedFiles()) > 0) {
            $postData['files'] = $this->getFilesUploaded($req);
        }

        return $postData;
    }

    /**
     * getHeaders
     *
     * @param  mixed $req
     *
     * @return Array
     */
    private function getHeaders(Request $req): Array {
        return $req->getHeaders();
    }

    /**
     * getFilesUploaded
     *
     * @param  mixed $request
     *
     * @return Array
     */
    private function getFilesUploaded(Request $request): Array {
        $files = [];
        $item = [];
        $uploadedFiles = $request->getUploadedFiles();
        foreach($uploadedFiles as $file) {
            $item['file'] = $file->getStream();
            $item['name'] = $file->getClientFilename();
            $item['mime'] = $file->getClientMediaType();
            $item['size'] = $file->getSize();
            $files[] = $item;
        }

        return $files;
    }

    /**
     * getPayload
     *
     * @param  mixed $request
     * @param  mixed $args
     *
     * @return Array
     */
    private function getPayload(Request $request, $args): Array {
        if ($request->isPost() === true) {
            return $this->getPostData($request);
        }

        if ($request->isGet() === true) {
            return $this->getPayloadOfGetMethod($request, $args);
        }

        throw new InvalidMethodType("invalid.http.method");
    }

    /**
     * getPayloadOfGetMethod
     *
     * @param  mixed $request
     * @param  mixed $args
     *
     * @return Array
     */
    private function getPayloadOfGetMethod(Request $request, $args): Array {
        $data['headers'] = $this->getHeaders($request);
        if (is_array($args) === true && count($args) > 0) {
            $data['data'] = $args;

            return $data;
        }

        $data['data'] = $request->getQueryParams();

        return $data;
    }
}
