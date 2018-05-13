<?php
namespace Cs\Router\Util;

use \Exception;
use Pimple\Container as Pimple;
use Cs\Router\Services\Cors;
use Cs\Router\Services\RequestHandler;
use Cs\Router\Util\Assert;
use \Slim\App as Slim;

class App extends RequestHandler {
    public function __construct(Slim $slim, Pimple $container, $routes, $cors = []) {
        $this->app = $slim;
        $this->containers = $container;
        Assert::arrayNotEmpty($routes, 'routes.must.have.array');
        $this->routes = $routes;
        if (count($cors) > 0) {
            $this->setCors($cors);
        }

        $this->processRequest();
    }

    public function processRequest() {
        $this->mapRoutes();
    }

    private function setCors($cors) {
        $this->app->add(Cors::routeMiddleware($cors));
    }

    public function run() {
        $this->app->run();
    }
}
