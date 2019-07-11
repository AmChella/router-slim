# Slim-Router

[![Latest Stable Version](https://poser.pugx.org/router/slim/v/stable)](https://packagist.org/packages/router/slim)
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
  method: post|get
  invoke: [class]->[function]
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
-
  uri: /pattern/pattern
  method: post
  invoke: [class]->[function]

e.g
-
  uri: /files
  method: post
  invoke: UploadService->upload

  <?php
    namespace `YourNamespace`;

    use Slim\Http\file;

    Class UploadService {
      public function upload($data) {
        $filename = $this->saveFiles('./uploads', $data['files']);
      }

      public function savefiles($directory, UploadedFile $file) {
        $extn = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $basename = pathinfo($file->getClientFilename(), PATHINFO_FILENAME);
        $filename = sprintf('%s.%s', $basename, $extension);
        $file->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
      }
    }
```

### Here you go

Happy routing.
