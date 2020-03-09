# Slim-Router

[![Latest Stable Version](https://poser.pugx.org/router/slim/v/stable?format=flat-square)](https://packagist.org/packages/router/slim)
[![Total Downloads](https://poser.pugx.org/router/slim/downloads?format=flat-square)](https://packagist.org/packages/router/slim)
[![Latest Unstable Version](https://poser.pugx.org/router/slim/v/unstable?format=flat-square)](https://packagist.org/packages/router/slim)
[![License](https://poser.pugx.org/router/slim/license?format=flat-square)](https://packagist.org/packages/router/slim)

application with Slim Framework

### Installation

Use [Composer](https://getcomposer.org/)

### Do Composer Require as below
```
composer require router/slim
```

### Create instance
```
use \Cs\Router\Util\App as Router;

$app = new Router(
            $slim, $containerServices,
            $routes, $cors -> [array] optional
        );
$app->run();
```

##### Note: The router will support `GET`|`POST`|`PUT`|`OPTIONS` alone
---

### Create a routes as below and send the routes as array.
```
-
  url: /test[/{return}] json|raw|download (optional. Default is raw)
  method: get|post|put|head|delete
  invoke: [class]->[function]
```
* **`url`** is the path of route
* **`method`** is http method like get|post. you can specify it either small or caps
* **`invoke`** is callback placeholder. *`class`* is a service name and *`function`* is callback method. If you were using DI container then provide full path of the service name.
* **`[/{return}]`** is how response body would be. *`json`* will return response as *`json`*, *`raw`* will return the text body, *`download`* will force the response as downloadable stream.
* if you specified return is *`download`* and your response body should be like blow

***
### Your service Response should like below
***

##### Response body -> `Download` 
**Note**: Response should be in a array.
```
[
  'file' => filestream,
  'fileSize' => sizeoffile,
  'ContentType' => mime,
  'fileName' => in what name it should to streamed as response,
  'statusCode' => integer (optional)
]
```

##### Response body -> `json`
```
[
  'statusCode' => integer (optional),
  'data' => data [array|string]
]
```

##### Response body -> `raw`
```
  'statusCode' -> (string)
```

#### Example
```
-
  url: /test[/{return}]
  method: post
  invoke: test->welcome
-
  url: /test/{token}[/{return}]
  method: get
  invoke: test->getData
-
  url: /test/download[/{return}] (You should specify return is download)
  method: get
  invoke: test->getFile
```
 ### Cors values
```
 cors:
    allow_credentials: 'true'
    accept_headers: Content-Type, X-Requested-With
    origin:
        - http://domain.name.com
```

***
### Router request body should like below
***

##### Request body -> `GET`
```
uri: test/{name}?job=test
method: get
```
```
[
  'headers' => [
    'headers'
  ],
  'params' => [
    'name' => 'chella',
    'job' => 'test'
  ]
]
```
##### Request body -> `POST`
```
uri: test/{name}
method: post
```
```
[
  'data' => [
    'post data'
  ],
  'headers' => [
    'headers'
  ],
  'params' => [
    'name' => 'chella
  ]
]
```
##### Request body -> `PUT`
```
uri: test/{name}
method: put
```
```
[
  'data' => 'stream',
  'headers' => [
    'headers'
  ],
  'params' => [
    'name' => 'chella
  ]
]
```
##### Request body -> `POST`
```
uri: test/{name}
method: post (fileupload)
```
```
[
  'data' => [
    'postdata'
  ],
  'headers' => [
    'headers'
  ],
  'params' => [
    'name' => 'chella
  ],
  'files' => [
      [
       'file' => fileStream,
       'name' => fileName,
       'mime' => mediaType,
       'size' => size
      ]
      [
        ...
      ]
  ]
]
```

#### Example
```
<?php
require_once 'vendor/autoload.php';

use EqnComparer\Util\Container;
use \Cs\Router\Util\App as Router;
$yaml = __DIR__ . '/app/Tnq/EqnComparer/Config/Routes.yaml';
$appYaml = __DIR__ . '/app/Tnq/EqnComparer/Config/Config.yaml';
$context = Container::getContext(
    ['routesConfig' => $yaml, 'appConfig' => $appYaml]
);
$routes = $context['routesConfig'];
$cors = $context['appConfig']['cors'];
try {
    $app = new Router([], $context, $routes, $cors);
    $app->run();
}
catch(Exception $e) {
    echo $e->getMessage();
}
```

### Here you go!
&#9786; Happy routing
