<?php
namespace Cs\Router\Util;

use \Exception;
use Cs\Router\Services\Cors;
use Cs\Router\Services\RequestHandler;
use Cs\Router\Services\ResponseHandler;
use Slim\Factory\AppFactory;
use Slim\Http\Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;

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
        $this->debug = array_key_exists('debug', $settings) === true ? 
        $settings['debug'] === 1 ? true : false : false;
    }

    private function setCors($cors): Void {
        $this->app->add(Cors::routeMiddleware($cors, $this->app));
    }

    public function run(): Void {
        try {
            $this->routeToService($this->routes);
            $this->app->run();
        } catch(HttpMethodNotAllowedException|HttpNotFoundException $e) {
            $errorCode = $e->getCode();
            $traceMessage = $e->getMessage();
            $result = [
                'error' => sprintf("Routing not found or %s", $traceMessage),
                'errorId' => $errorCode,
                'status' => false
            ];
            header("Content-Type: application/json");
            http_response_code(404);
            echo json_encode($result);
        } 
        catch (\Exception $e) {
            $errorCode = $e->getCode();
            $traceMessage = $e->getTraceAsString();
            $errorMessage = $e->getMessage();
            $result = [
                'error' => $traceMessage,
                'message' => $errorMessage,
                'errorId' => $errorCode,
                'status' => false
            ];
            header("Content-Type: application/json");
            http_response_code(500);
            echo json_encode($result);
        }
    }
}
