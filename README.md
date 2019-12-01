# Simple RESTful-API PHP skeleton
Easy to use and lightweight RESTful-API skeleton written on PHP7 with authorization by token in header.
Supports HTTP-methods: GET, POST, PUT, DELETE and validation the data transmitted in the request.

[![CodeFactor](https://www.codefactor.io/repository/github/alevar88/simpleapi/badge)](https://www.codefactor.io/repository/github/alevar88/simpleapi)

## Introduction
* [Install](https://github.com/alevar88/simpleapi#install)
* [Usage](https://github.com/alevar88/simpleapi#usage)
* [Example Model](https://github.com/alevar88/simpleapi#example-model)
* [Example Controller](https://github.com/alevar88/simpleapi#example-controller)
* [Request and Response](https://github.com/alevar88/simpleapi#request-and-response)
* [Authorization by token](https://github.com/alevar88/simpleapi#authorization-by-token)
* [Validation and validators](https://github.com/alevar88/simpleapi#validation-and-validators)
* [Command line interface](https://github.com/alevar88/simpleapi#command-line-interface)

## Install
Clone source code from the repository
```shell script
git clone https://github.com/alevar88/simpleapi.git
```
then create a class loader through the composer
```shell script
cd ./simpleapi; composer dump-autoload --optimize
```
## Usage
Copy configuration file and edit
```shell script
cp app/configs/example.config.php app/configs/your_api_name.config.php
```
```php
'router' => array(
    /**
     * If you use subdirectories in your virtual host 
     * configuration "https://domain.tld/api/my-api-method"
     * enter the name of the subdirectory "/api" or leave it blank
     */
    'escPath' => '/api',
    'routes' => array(
        //Name and settings of the API method
        '/my-api-method' => array(
            'handler' => 'app\controllers\ExampleController',
            //Request validation rules (optional)
            //You can leave these settings blank
            'rules' => array(
                //By default for all types of requests
                'default' => array(
                    'id' => 'integer', //or combine 'integer|required'
                    'field1' => 'alphanum',
                    'field2' => 'integer',
                 ),
                'get' => array(
                    'field1' => 'required',
                 ),
                'post' => array(
                    'field1' => 'required',
                    'field2' => 'required',
                 ),
                'put' => array(
                    'id' => 'required',
                 ),
                'delete' => array(
                    'id' => 'required',
                 ),
            ),
        ),
    ),
    '/my-other-api-method' => array(
        ...
    ),
),
'database' => array(
    'dsn' => 'mysql:dbname=your_db;host=your_host',
    'username' => 'your_db_user',
    'password' => 'secret',
    //PDO options. Array or NULL
    'options' => null,
    //Do something on connect to db (optional)
    'onConnect' => function(\PDO $pdo) {}
)
```
create `index.php` as entry point for the application
```php
<?php

require_once 'vendor/autoload.php';

//See for example app/configs/example.config.php
$config = require_once 'app/configs/your_api_name.config.php';

$bootstrap = app\Bootstrap::load($config);

//You can do something before run the application (optional)
$bootstrap->beforeRun(function ($container) {
    //Do something before
});

//And also you can do something on shutdown the application (optional)
$bootstrap->onShutdown(function ($container) {
    //Do something on shutdown
}); 

//Run an application entry point
$bootstrap->run();
```

## Example model
Example model class `app/models/ExampleModel.php`

_Note: Each model has base methods `load()`, `loadAll()`, `save()` and `delete()` for CRUD operations which yourself can override._
```php
<?php

namespace app\models;

class ExampleModel extends Model
{
    protected $table = 'table_name';
    
    //Primary key
    protected $pkey = 'id';
    
    //Bind table fields
    protected $fields = array(
        'id', 'field1', 'field2', 'field3',
    );
}
```

## Example Controller
Example controller class `app/controllers/ExampleController.php`
```php
<?php

namespace app\controllers;

use app\models\ExampleModel;

class ExampleController extends Controller
{
    //Optional
    public function onRequest()
    {
        //Do something before call controller method
    }
    
    //Optional
    public function onResponse()
    {
        //Do something after call controller method
    }

    public function get(): string
    {
        $result = $this->getModel(ExampleModel::class)->loadAll($this->request->getParams())->toArray();

        //Or you can use without request params. Params automatically added in the model.
        //Also toArray accepts field names, example: toArray(array('id', 'field1', 'field2', etc...))
        $result = $this->getModel(ExampleModel::class)->loadAll()->toArray();

        if (empty($result)) {
            return $this->response->sendNotFound();
        }

        //Short version
        return $this->response->sendResult($result);

        //Full version
        return $this->response
                    ->withCode(/* Status code */)
                    ->withError(/* Error message or errors array */)
                    ->withResponse(/* Response data. */)
                    ->send();
    }
    
    public function post(): string
    {
        $model = $this->getModel(ExampleModel::class)->load();
        if ($model->isLoaded()) {
            return $this->response->sendAlreadyExists();
        }
        //Last insert id or 0
        $result = $model->save();
        return $this->response
                ->withCode(201) //Send with Created status if successful.
                ->sendResult($result);
    }
    
    public function put(): string
    {
        $model = $this->getModel(ExampleModel::class)->load(array('id' => $this->request->getParam('id')));
        if (!$model->isLoaded()) {
            return $this->response->sendNotFound();
        }
        //Value in Primary key or 0
        $result = $model->import($this->request->getParams())->save();
        return $this->response->sendResult($result);
    }

    public function delete(): string
    {
        $model = $this->getModel(ExampleModel::class)->load($this->request->getParams(array('id')));
        if (!$model->isLoaded()) {
            return $this->response->sendNotFound();
        }
        //Boolean
        $result = $model->delete();
        return $this->response->sendResult($result);
    }
}
```

## Request and Response
cURL POST request
```shell script
curl -d "field1=some data 1&field2=some data 2&field3=some data 3" -X POST http://hostname:port/my-api-method
```
JSON response:
```json
{
  "requestId": "5dcc662ab4afa",
  "requestMethod": "POST",
  "apiName": "my-api-method",
  "status": 201,
  "errors": [],
  "response": 10249,
  "request": {
    "field1": "some data 1",
    "field2": "some data 2",
    "field3": "some data 3"
  }
}
```

## Authorization by token
Implement the `app\services\AuthorizationInterface` in your model or other handler and create `isAuth()` method
```php
<?php

namespace app\models;

use app\services\AuthorizationInterface;

class UsersModel extends Model implements AuthorizationInterface
{
    protected $table = 'users';

    protected $fields = array(
        'id', 'login', 'api_token',
    );
    
    public function isAuth(): bool
    {
        $token = $this->container->get('request')->getToken();
        $this->load(array('api_token' => $token));
        return $this->isLoaded();
    }
}
```
then configure in the config `app/configs/your_api_name.config.php`
```php
return array(
    ...
    'authorization' => array(
        'enable' => true,
        'header' => 'Authorization', //Name of header
        'handler' => '\app\models\UsersModel',
    ),
    ...
);
```

## Validation and validators
You can add the necessary validators yourself in configuration file
```php
<?php
//See for example 'validators' in app/configs/example.config.php
return array(
    ...
    'validators' => array(
        //Validator
        array(
            'Validator name', 
            'Error message', //Message on validation failure
            /**
             * @param $value validation data.
             * @param app\services\ValidationInterface $validation optional for nested validators.
             * @return bool callable must return only boolean type.
             */
            function ($value, $validation):bool {
                /* Do something for validation */ 
            }
        ),
    ),
    ...
);
```
or use `app\services\Validator` class in your code
```php
<?php

$container->get('validation')->registerValidator(
    new Validator(
        'integer', //Validator name
        'Must be integer type', //Error message
        function ($value):bool { 
            return (!is_array($value) && preg_match('/^\d+$/', "{$value}"));
        }
    )
);
```
example validation in your code
```php
<?php

$validation = $container->get('validation');
$validationRules = array('id' => 'integer');
$validationParams = $container->get('request')->getParams(array('id'));
$validation->withRules($validationRules)->withParams($validationParams);
if (!$validation->validate()) {
    return $container->get('response')->sendFailedValidate();
}        
```
response to client looks like this:
```
Sent headers:
HTTP/1.1 400 Client Error: Bad Request
Content-Type: application/json
```
and JSON response
```json
{
  "requestId": "5dd435e18743c",
  "requestMethod": "POST",
  "apiName": "my-api-method",
  "status": 400,
  "errors": {
    "id": "Must be integer type"
  },
  "response": [],
  "request": {
    "id": "some string"
  }
}
```

## Command line interface
Create example command `app/commands/ExampleCommand.php` and implement `app\services\CommandInterface`
```php
<?php

namespace app\commands;

use app\services\CommandInterface;
use app\services\ContainerInterface;

class ExampleCommand implements CommandInterface
{
    private $container;

    private $name = 'example';

    private $status = 0;

    public function __construct(ContainerInterface $container) 
    {
        $this->container = $container;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    //Help text for --help command
    public function getHelp() : string
    {
        return "";
    }

    public function run()
    {
        $request = $this->container->get('request');
        $param1 = $request->getParam('param1', FILTER_SANITIZE_STRING);
        $param2 = $request->getParam('param2', FILTER_SANITIZE_STRING);
        echo "{$param1} {$param2}!\r\n";
    }
}
```
register command in config
```php
array(
    ...
    'commands' => array(
        'app\commands\ExampleCommand',
    ),
    ...
);
```
and call command in shell
```shell script
# php index.php --command example --param1 Hello --param2 world
Hello world!
```