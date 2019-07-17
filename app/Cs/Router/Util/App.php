<?php
namespace Cs\Router\Util;

use \Exception;
use Pimple\Container as Pimple;
use Cs\Router\Services\Cors;
use Cs\Router\Services\RequestHandler;
use \Slim\App as Slim;

/**
 * @category Router_Package_For_Slim_3
 * @package Its_A_Opensource_For_Slim_Router
 * @author Chella S <2chellaa@gmail.com>
 * @license MIT License
 * @link http://url.com
 */

Class App extends RequestHandler {
    public function __construct(
        Slim $slim, Pimple $container, $routes, $cors = []
    ) {
        $this->app = $slim;
        $this->containers = $container;
        $this->arrayNotEmpty($routes, 'routes.must.have.array');
        $this->routes = $routes;
        if (count($cors) > 0) {
            $this->setCors($cors);
        }
    }

    private function setCors($cors): Void {
        $this->app->add(Cors::routeMiddleware($cors));
    }

    public function run(): Void {
        $this->assignRoutesToService();
        $this->app->run();
    }
}
