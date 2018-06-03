<?php
namespace Cs\Router\Services;

// https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
class Cors {
    protected $settings;

    public function __construct($settings = []) 
    {
        $this->settings = array_merge([
                'origin' => '*',    // Wide Open!
                'allowMethods' => 'GET,HEAD,PUT,POST,DELETE'
            ], $settings
        );
    }

    protected function setOrigin($req, $rsp): Object 
    {
        $origin = $this->settings['origin'];
        if (is_callable($origin) === true) {
            // Call origin callback with request origin
            $origin = call_user_func(
                $origin,
                $req->getHeader("Origin")
            );
        }

        // handle multiple allowed origins
        if (is_array($origin) === true) {
            $allowedOrigins = $origin;
            $origin = null;
            // but use a specific origin if there is a match
            foreach ($allowedOrigins as $allowedOrigin) {
                foreach ($req->getHeader("Origin") as $reqOrig) {
                    if ($allowedOrigin === $reqOrig) {
                        $origin = $allowedOrigin;
                        break;
                    }
                }
                if (is_null($origin) === false) {
                    break;
                }
            }

            if (is_null($origin) === true) {
                // default to the first allowed origin
                $origin = reset($allowedOrigins);                
            }
        }

        return $rsp->withHeader('Access-Control-Allow-Origin', $origin);
    }

    protected function setExposeHeaders($req, $rsp): Object
    {
        if (isset($this->settings['exposeHeaders']) === true) {
            $rsp = $rsp->withAddedHeader(
                'Access-Control-Expose-Headers', $this->settings['exposeHeaders']
            );
        }

        return $rsp;
    }
    
    protected function setMaxAge($req, $rsp) 
    {
        if (isset($this->settings['maxAge']) === true) {
            $rsp = $rsp->withHeader(
                'Access-Control-Max-Age', $this->settings['maxAge']
            );
        }

        return $rsp;
    }

    protected function setAllowCredentials($req, $rsp) 
    {
        if (
            isset($this->settings['allowCredentials']) === true && 
            $this->settings['allowCredentials'] === true
        ) {
            $rsp = $rsp->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $rsp;
    }

    protected function setAllowMethods($req, $rsp) 
    {
        if (isset($this->settings['allowMethods']) === true) {
            $rsp = $rsp->withHeader(
                'Access-Control-Allow-Methods', $this->settings['allowMethods']
            );
        }

        return $rsp;
    }

    protected function setAllowHeaders($req, $rsp) 
    {
        $allowHeaders = $this->settings['allowHeaders'] ?? 
        $req->getHeader("Access-Control-Request-Headers");

        if (isset($allowHeaders) === true) {
            $rsp = $rsp->withHeader('Access-Control-Allow-Headers', $allowHeaders);
        }

        return $rsp;
    }

    protected function setCorsHeaders($req, $rsp): Object 
    {
        if ($req->isOptions() === true) {
            $rsp = $this->setOrigin($req, $rsp);
            $rsp = $this->setMaxAge($req, $rsp);
            $rsp = $this->setAllowCredentials($req, $rsp);
            $rsp = $this->setAllowMethods($req, $rsp);
            $rsp = $this->setAllowHeaders($req, $rsp);
            return $rsp;
        } 

        $rsp = $this->setOrigin($req, $rsp);
        $rsp = $this->setExposeHeaders($req, $rsp);
        $rsp = $this->setAllowCredentials($req, $rsp);

        return $rsp;
    }

    public function __invoke($request, $response, $next) 
    {
        $response = $this->setCorsHeaders($request, $response);
        if ($request->isOptions() === false) {
            $response = $next($request, $response);
        }

        return $response;
    }

    public static function routeMiddleware($settings = [])
    {
        $cors = new Cors($settings);
        return $cors;
    }
}
