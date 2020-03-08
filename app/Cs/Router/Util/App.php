<?php
namespace Cs\Router\Util;

use \Exception;
use Cs\Router\Services\Cors;
use Cs\Router\Services\RequestHandler;
use Cs\Router\Services\ResponseHandler;
use Slim\Factory\AppFactory;

/**
 * @category Router_Package_For_Slim_3
 * @package Its_A_Opensource_For_Slim_Router
 * @author Chella S <2chellaa@gmail.com>
 * @license MIT License
 * @link http://url.com
 */

Class App extends RequestHandler {

    public function __construct(
        $slim, $container, $routes, $cors = [], $settings = []
    ) {
        $this->initApp($slim, $settings);
        $this->containers = $container;
        $this->arrayNotEmpty($routes, 'routes.should.be.an.array');
        $this->routes = $routes;
        if (count($cors) > 0) {
            $this->setCors($cors);
        }

        $this->responseHandler = new ResponseHandler();
    }

    private function initApp($slim, Array $settings): Void {
        $this->app = AppFactory::create();
    }

    private function setCors($cors): Void {
        $this->app->add(Cors::routeMiddleware($cors, $this->app));
    }

    public function run(): Void {
        try {
            $this->routeToService($this->routes);
            $this->app->run();
        } catch (\Exception $e) {
            $statusCode = $e->getCode();
            $traceMessage = $e->getTraceAsString();
            $message = $e->getMessage();
            throw new Exception($message, $statusCode);
        }
    }
}
