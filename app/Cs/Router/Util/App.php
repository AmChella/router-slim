<?php
namespace Cs\Router\Util;

use \Exception;
use Pimple\Container as Pimple;
use Cs\Router\Service\Cors;
use Cs\Router\Service\RequestHandler;

class App extends RequestHandler {    
    private $reqHandler;
    private $app;

    public function __construct(SlimApplication $slim, $routes, $cors = []) {
        $this->app = $slim;
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
