<?php
namespace Cs\Router\Service;

use Pimple\Container as Pimple;
use Slim\App as SlimApplication;
use Cs\Router\Service\Cors;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Router {
    private $app;
    private $requestHandler;

    public function __construct(Pimple $context) {
        $this->app = new SlimApplication();
        $this->setCors($context['cors']);
        $this->mapRequest($context);
    }

    private function setCors($cors) {
        $this->app->add(Cors::routeMiddleware($cors));
    }

    private function mapRequest($c) {
        $routes = $c['routes'];
        $map = [];
        foreach($routes as $route) {
            $map['url'] = $route['url'];
            list($service, $func) = explode("->", $route['invoke']);
            $map['method'] = $route['method'];
            $map['service'] = $c['object'][$service];
            $map['func'] = $func;
            $this->mapRequestToService($map);
        }
    }

    private function mapRequestToService(array $map) {
        $ins = $this;
        $callable = function(Request $request, Response $response, $args) use($map, $ins) {
            $args = call_user_func([$ins, 'getRequestInput'], $request, $args);
            $result = call_user_func([$map['service'], $map['func']], $args);
            return call_user_func([$ins, 'setResponse'], $response, $result);
        };

        $pattern = $map['url'];
        self::$app->map([$map['method']], $pattern, $callable);
    }

    public function getRequestInput($request, $args) {
        $input = [];
        if ($request->getMethod() === "POST") {
            $input = $request->getParsedBody();
            if (isset($input['json']) === true) {
                $input = json_decode($input['json'], true);
            }
        }
        elseif (is_array($args) === true) {
            $input = $args;
        }
        else {
            $input = json_encode($args);
        }

        return $input;
    }

    public function setResponse($response, $result) {
        $data = ['success' => true, 'data' => $result];
        return $response->withJson($data);
    }

    public function run() {
        $this->app->run();
    }
}
