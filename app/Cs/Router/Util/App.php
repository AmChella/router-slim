<?php
namespace Cs\Router\Util;

use \Exception;
use Pimple\Container as Pimple;
use Cs\Router\Services\Cors;
use Cs\Router\Services\RequestHandler;
use Cs\Router\Util\Assert;
use \Slim\App as Slim;

/**
 * @category Router_Package_For_Slim_3
 * @package Its_A_Opensource_For_Slim_Router
 * @author Chella S <2chellaa@gmail.com>
 * @license MIT License
 * @link http://url.com
 */

class App extends RequestHandler {
    public function __construct(Slim $slim, Pimple $container, $routes, $cors = []) 
    {
        $this->app = $slim;
        $this->containers = $container;
        Assert::arrayNotEmpty($routes, 'routes.must.have.array');
        $this->routes = $routes;
        if (count($cors) > 0) {
            $this->setCors($cors);
        }

        $this->processRequest();
    }

    public function processRequest() 
    {
        $this->mapRoutes();
    }

    private function setCors($cors) 
    {
        $this->app->add(Cors::routeMiddleware($cors));
    }

    public function run() 
    {
        $this->app->run();
    }
}
