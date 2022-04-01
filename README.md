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
composer require router/slim {version}
```

### Create instance
```
use \Cs\Router\Util\App as Router;

$app = new Router(
    $containerServices,
    $routes, $cors -> [array] optional
);
$app->run();
```

##### Note: The router will support `GET`|`POST`|`PUT`|`OPTIONS` alone
---

### Create a routes as below and send the routes as array.
```
-
  url: /test[/{return}] (optional)
  method: get|post|put|head|delete
  routeBeforeInvoke:
    - [class]->[function]
    ...
  invoke: [class]->[function]
  return: json|raw|download (*optional)
```
* **`url`** is the path of route
* **`method`** is http method like get|post. you can specify it either small or caps
* **`routeBeforeInvoke`** is a special routing like middleware(optional). this is will be invoked before actual method call and the response will to forwared with method|function name as **`array`**.
* **`invoke`** is callback placeholder. *`class`* is a service name and *`function`* is callback method. If you were using DI container then provide full path of the service name.
* **`[/{return}]`** is a optional routing, which is used to return a response as *`json`* , *`raw`*, *`download`*. You can specify it either uri routing or explicitly in array as mentioned above.
* *`download`* is will force the response as downloadable request. Note* its return values should be like below in the response section.

***
### Your service Response should like below
***

##### If return type is `Download` then the method response should be Array like below
```
[
  'file' => File Content,
  'fileSize' => File Size,
  'ContentType' => mime,
  'fileName' => FileName (in what name it should be downloaded),
  'statusCode' => integer (optional)
]
```

##### If return type is `Download` then the method response should be array like below
```
[
  'statusCode' => integer (optional),
  'data' => data [array]
]
```

##### If return type is `raw` then the method response should be a string like below
```
  'response' -> (string)
```

#### Example 1
```
##### specified, routing level return type & routeBeforeIvoke is a kind of middleware it should be called before actual routing method and their reponses are returned in their own method name.
-
  url: /test[/{return}]
  routeBeforeInvoke:
    - test->Validate
    - test->Validate2
  method: post
  invoke: test->welcome
  return: json

req -> /test/json
  * test->Validate get called returns ['Validate' => [response]]
  * test->Validate get called returns  ['Validate' => [response], 'Validate2' => [response]]
  * test->welcome finally called your responses and it should be array of thing bcz, return type is specified as json * [/{return}] -> json * 
  Note* static return type will be overrided by dynamic routing (URI routing).
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

## Stargazers over time

[![Stargazers over time](https://starchart.cc/amchella/router-slim.svg)](https://starchart.cc/amchella/router-slim)


### Here you go!
&#9786; Happy routing
