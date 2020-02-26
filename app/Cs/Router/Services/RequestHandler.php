<?php
namespace Cs\Router\Services;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Cs\Router\Exception\InvalidRoute;
use Cs\Router\Exception\InvalidMethodType;
use Cs\Router\Util\Assert;

class RequestHandler extends Assert {
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
        if (!preg_match('/[a-zA-Z]{3,}(->)[a-zA-Z]{5,}/', $route['invoke'])) {
            throw new InvalidRoute('route.uri.invalid');
        }

        list($service, $func) = explode("->", $route['invoke']);
        $this->validateServiceHasValidCallback($service, $func);
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
        String $class, String $method
    ): Void {
        $msg = sprintf('func.%s.not.found', $method);
        $this->hasMethod($this->getClass($class), $method, $msg);
        $msg = sprintf('func.%s.not.callable', $method);
        $this->isCallable($this->getClass($class), $method, $msg);
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
                [$instance->getClass($map['service']), $map['func']], $args
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

    private function getPutData(Request $req, $args): Array {
        $data = [];
        $size = $req->getBody->getSize();
        $data['data'] = $req->getBody()->read($size);
        $data['headers'] = $this->getHeaders($req);
        $data['params'] = $this->getPayloadOfGetMethod($req, $args);

        return $data;
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
            $data = $this->getPostData($request);
            $paramValues = $this->getPayloadOfGetMethod($request, $args);
            $data['params'] = $paramValues['params'] ?? "";

            return $data;
        }

        if ($request->isGet() === true) {
            return $this->getPayloadOfGetMethod($request, $args);
        }

        if ($request->isPut() === true) {
            return $this->getPutData($request, $args);
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
            $data['params'] = $args;

            return $data;
        }

        $data['params'] = $request->getQueryParams();

        return $data;
    }
}
