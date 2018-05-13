<?php
namespace Slimple\Slim\Util;

use \Exception;
use Pimple\Container as Pimple;
use app\Slim\Service\Router;

class Container {
    private $application;

    public function __construct(Array $routes, Pimple $instances, $cors = []) {
        $this->application = [];
        if (empty($routes) === true) {
            throw new Exception('routes.value.is.empty');
        }

        array_push($this->application, [
            'routes' => $routes, 'cors' => $cors, 'object' => $instances
            ]);
        return $this->initialize($this->application);
    }

    private function initialize($a) {

        $a['router'] = function($c) use($a) {
            return new router($a);
        };

        $a['requestHandler'] = function($c) use($a) {
            return new RequestHandler($a);
        };

        $a['cors'] = function ($c) use ($a) {
            $corsValues = $a['corsHandler'];
            $allowedOrigin = $this->getAllowedOrigin(
                $corsValues['origin']
            );

            $corsOptions = [
                'origin' => $allowedOrigin,
                'allowHeaders' => $corsValues['accept_headers'],
                'allowCredentials',  $corsValues['allow_credentials']
            ];

            return $corsOptions;
        };

        $a['corsHandler'] = function () use ($a) {
            $hasCrossOrigin = array_key_exists('cors', $a['cors']);
            if (empty($hasCrossOrigin) === true) {
                return [
                    'origin' => '*',
                    'allowCredentials' => 'false',
                    'aollowHeaders' => 'Content-Type, X-Requested-With',
                ];
            }

            return $a['cors'];
        };

        return $a;
    }

    private function getOriginHeader() {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            return $_SERVER['HTTP_ORIGIN'];
        }
        return null;
    }

    private function getAllowedOrigin($allowedOriginList) {
        $originHeader = $this->getOriginHeader();
        if (empty($originHeader) ) {
            return null;
        }

        $matchedKey = array_search($originHeader, $allowedOriginList);
        if ($matchedKey >= 0) {
            $matchedOrigin = $allowedOriginList[$matchedKey];
            return $matchedOrigin;
        }

        return null;
    }
}
