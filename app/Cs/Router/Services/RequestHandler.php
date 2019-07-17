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

    public function assignRoutesToService($routes): Void {
        foreach ($routes as $route) {
            $this->validateRoute($route);
            $map['url'] = $route['uri'];
            list($service, $func) = explode("->", $route['invoke']);
            $map['method'] = $route['method'];
            $map['service'] = $service;
            $map['func'] = $func;
            $this->assignCallback($map);
        }
    }

    private function validateRoute(Array $route): Void {
        $this->isHashArray($route, 'routes.not.an.array');
        $this->isArrayKeyExist('invoke', $route, 'invoke.key.not.found');
        $this->isArrayKeyExist('uri', $route, 'uri.not.found');
        $this->isArrayKeyExist('method', $route, 'method.not.found');
        if (!preg_match('/[a-zA-Z]{3,15}(->)[a-zA-Z]{5,}/', $route['invoke'])) {
            throw new InvalidRoute('route.uri.invalid');
        }

        list($service, $func) = explode("->", $route['invoke']);
        $this->validateServiceHasValidCallback($service, $func);
    }

    public function validateServiceHasValidCallback($class, $method): Void {
        $msg = sprintf('func.%s.not.found', $method);
        $this->hasMethod($this->containers[$class], $method, $msg);
        $msg = sprintf('func.%s.not.callable', $method);
        $this->isCallable($this->containers[$class], $method, $msg);
    }

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
                $response, $result
            );
        };

        $pattern = $map['url'];
        $this->app->map([$map['method']], $pattern, $callable);
    }

    public function getPostData($req): Array {
        $postData = [];
        $postData = $req->getParsedBody();
        if (count($req->getUploadedFiles()) > 0) {
            $postData['files'] = $this->getFilesUploaded($req);
        }

        return $postData;
    }

    public function getFilesUploaded(Request $request): Array {
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

    public function getPayload($request, $args): Array {
        if ($request->isPost() === true) {
            return $this->getPostData($request);
        }

        if ($request->isGet() === true) {
            return $this->getGetPayload($request, $args);
        }

        throw new InvalidMethodType("invalid.http.method");
    }

    public function getGetPayload($request, $args): Array {
        if (is_array($args) === true && count($args) > 0) {
            return $args;
        }

        return $request->getQueryParams();
    }
}
