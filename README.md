# Slim-Router

[![Latest Stable Version](https://poser.pugx.org/router/slim/v/stable?style=for-the-badge)](https://packagist.org/packages/router/slim)
[![Total Downloads](https://poser.pugx.org/router/slim/downloads)](https://packagist.org/packages/router/slim)
[![Latest Unstable Version](https://poser.pugx.org/router/slim/v/unstable)](https://packagist.org/packages/router/slim)
[![License](https://poser.pugx.org/router/slim/license)](https://packagist.org/packages/router/slim)

application with Slim Framework

### Installation

Use [Composer](https://getcomposer.org/)

### Do Composer Require as below
```
composer require router/slim
```

### Create your app routes as below in a yaml
```
-
  uri: /
  method: get|post
  invoke: [class]->[function]
-
  uri: /pattern/pattern
  method: post
  invoke: [class]->[function]
  return: raw|json|download (default is json)
    e.g
      return -> raw
        response should be array, `data` key is mandatory
      return -> json
        response should be array, `status` key is mandatory and should be in boolean
      return -> download
        response should be array, `file`, `fileSize`, `ContentType` and `fileName` keys are mandatory and should be in boolean
          `file` file stream
```
 ### Cors values
```
 cors:
    allow_credentials: 'true'
    accept_headers: Content-Type, X-Requested-With
    origin:
        - http://domain.name.com
```
### Create instance
```
use \Cs\Router\Util\App as Router;

$app = new Router(
            $slim, $pimpleServices,
            $routes, $corsClosure -> optional
        );
$app->run();
```
### File upload
```
  in the param, uploaded files are available in the files key.
```

### Here you go

Happy routing.
