# Slim-Router

[![Latest Stable Version](https://poser.pugx.org/router/slim/v/stable)](https://packagist.org/packages/router/slim)
[![Total Downloads](https://poser.pugx.org/router/slim/downloads)](https://packagist.org/packages/router/slim)
[![Latest Unstable Version](https://poser.pugx.org/router/slim/v/unstable)](https://packagist.org/packages/router/slim)
[![License](https://poser.pugx.org/router/slim/license)](https://packagist.org/packages/router/slim)

application with Slim Framework

### Installation

Use [Composer](https://getcomposer.org/)
### Create routes as below
-
  uri: /
  method: get|post
  invoke: [class]->[function]
-
  uri: /pattern/pattern
  method: post|get
  invoke: [class]->[function]
  
 ### Cors will have as below
 
 cors:
    allow_credentials: 'true'
    accept_headers: Content-Type, X-Requested-With
    origin:
        - http://domain.name.com

### Create instance as below

use \Cs\Router\Util\App as Router;

new Router(
            $slim, $pimpleServices, 
            $routes, $corsClosure -> optional
        );

 
