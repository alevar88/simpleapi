

# Esqueleto simple de API RESTful en PHP
Esqueleto de API RESTful fácil de usar y ligero, escrito en PHP7 con autorización por token en el encabezado.
Soporta métodos HTTP: GET, POST, PUT, DELETE y validación de los datos transmitidos en la solicitud.

[![CodeFactor](https://www.codefactor.io/repository/github/alevar88/simpleapi/badge)](https://www.codefactor.io/repository/github/alevar88/simpleapi)

## Introducción
* [Instalación](https://github.com/alevar88/simpleapi#install)
* [Uso](https://github.com/alevar88/simpleapi#usage)
* [Ejemplo de Modelo](https://github.com/alevar88/simpleapi#example-model)
* [Ejemplo de Controlador](https://github.com/alevar88/simpleapi#example-controller)
* [Solicitud y Respuesta](https://github.com/alevar88/simpleapi#request-and-response)
* [Autorización por token](https://github.com/alevar88/simpleapi#authorization-by-token)
* [Validación y validadores](https://github.com/alevar88/simpleapi#validation-and-validators)
* [Interfaz de línea de comandos](https://github.com/alevar88/simpleapi#command-line-interface)

## Instalación
Clona el código fuente del repositorio
```shell script
git clone https://github.com/alevar88/simpleapi.git
```
luego crea un cargador de clases a través de composer
```shell script
cd ./simpleapi; composer dump-autoload --optimize
```
## Uso
Copia el archivo de configuración y éditalo
```shell script
cp app/configs/example.config.php app/configs/your_api_name.config.php
```
```php
'router' => array(
    /**
     * Si usas subdirectorios en la configuración de tu host virtual 
     * "https://domain.tld/api/mi-metodo-api"
     * ingresa el nombre del subdirectorio "/api" o déjalo en blanco
     */
    'escPath' => '/api',
    'routes' => array(
        //Nombre y configuración del método de la API
        '/my-api-method' => array(
            'handler' => 'app\controllers\ExampleController',
            //Reglas de validación de la solicitud (opcional)
            //Puedes dejar estas configuraciones en blanco
            'rules' => array(
                //Por defecto para todos los tipos de solicitudes
                'default' => array(
                    'id' => 'integer', //o combinar 'integer|required'
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
    //Opciones de PDO. Array o NULL
    'options' => null,
    //Hacer algo al conectar a la BD (opcional)
    'onConnect' => function(\PDO $pdo) {}
)
```
crea `index.php` como punto de entrada para la aplicación
```php
<?php

require_once 'vendor/autoload.php';

//Consulta por ejemplo app/configs/example.config.php
$config = require_once 'app/configs/your_api_name.config.php';

$bootstrap = app\Bootstrap::load($config);

//Puedes hacer algo antes de ejecutar la aplicación (opcional)
$bootstrap->beforeRun(function ($container) {
    //Hacer algo antes
});

//También puedes hacer algo al cerrar la aplicación (opcional)
$bootstrap->onShutdown(function ($container) {
    //Hacer algo al cerrar
}); 

//Ejecutar el punto de entrada de la aplicación
$bootstrap->run();
```

## Ejemplo de Modelo
Clase de modelo de ejemplo `app/models/ExampleModel.php`

_Nota: Cada modelo tiene métodos base `load()`, `loadAll()`, `save()` y `delete()` para operaciones CRUD que puedes sobrescribir por ti mismo._
```php
<?php

namespace app\models;

class ExampleModel extends Model
{
    protected $table = 'table_name';
    
    //Clave primaria
    protected $pkey = 'id';
    
    //Vincular campos de la tabla
    protected $fields = array(
        'id', 'field1', 'field2', 'field3',
    );
}
```

## Ejemplo de Controlador
Clase de controlador de ejemplo `app/controllers/ExampleController.php`
```php
<?php

namespace app\controllers;

use app\models\ExampleModel;

class ExampleController extends Controller
{
    //Opcional
    public function onRequest()
    {
        //Hacer algo antes de llamar al método del controlador
    }
    
    //Opcional
    public function onResponse()
    {
        //Hacer algo después de llamar al método del controlador
    }

    public function get(): string
    {
        $result = $this->getModel(ExampleModel::class)->loadAll($this->request->getParams())->toArray();

        //O puedes usar sin parámetros de solicitud. Los parámetros se agregan automáticamente en el modelo.
        //Además, toArray acepta nombres de campos, ejemplo: toArray(array('id', 'field1', 'field2', etc...))
        $result = $this->getModel(ExampleModel::class)->loadAll()->toArray();

        if (empty($result)) {
            return $this->response->sendNotFound();
        }

        //Versión corta
        return $this->response->sendResult($result);

        //Versión completa
        return $this->response
                    ->withCode(/* Código de estado */)
                    ->withError(/* Mensaje de error o array de errores */)
                    ->withResponse(/* Datos de respuesta. */)
                    ->send();
    }
    
    public function post(): string
    {
        $model = $this->getModel(ExampleModel::class)->load();
        if ($model->isLoaded()) {
            return $this->response->sendAlreadyExists();
        }
        //Último ID insertado o 0
        $result = $model->save();
        return $this->response
                ->withCode(201) //Enviar con estado Created si es exitoso.
                ->sendResult($result);
    }
    
    public function put(): string
    {
        $model = $this->getModel(ExampleModel::class)->load(array('id' => $this->request->getParam('id')));
        if (!$model->isLoaded()) {
            return $this->response->sendNotFound();
        }
        //Valor en la clave primaria o 0
        $result = $model->import($this->request->getParams())->save();
        return $this->response->sendResult($result);
    }

    public function delete(): string
    {
        $model = $this->getModel(ExampleModel::class)->load($this->request->getParams(array('id')));
        if (!$model->isLoaded()) {
            return $this->response->sendNotFound();
        }
        //Booleano
        $result = $model->delete();
        return $this->response->sendResult($result);
    }
}
```

## Solicitud y Respuesta
Solicitud POST con cURL
```shell script
curl -d "field1=some data 1&field2=some data 2&field3=some data 3" -X POST http://hostname:port/my-api-method
```
Respuesta JSON:
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

## Autorización por token
Implementa la interfaz `app\services\AuthorizationInterface` en tu modelo u otro manejador y crea el método `isAuth()`
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
luego configura en el archivo `app/configs/your_api_name.config.php`
```php
return array(
    ...
    'authorization' => array(
        'enable' => true,
        'header' => 'Authorization', //Nombre del encabezado
        'handler' => '\app\models\UsersModel',
    ),
    ...
);
```

## Validación y validadores
Puedes agregar los validadores necesarios manualmente en el archivo de configuración
```php
<?php
//Consulta por ejemplo 'validators' en app/configs/example.config.php
return array(
    ...
    'validators' => array(
        //Validador
        array(
            'Validator name', 
            'Error message', //Mensaje en caso de fallo de validación
            /**
             * @param $value datos de validación.
             * @param app\services\ValidationInterface $validation opcional para validadores anidados.
             * @return bool la callable debe devolver solo tipo booleano.
             */
            function ($value, $validation):bool {
                /* Hacer algo para la validación */ 
            }
        ),
    ),
    ...
);
```
o usa la clase `app\services\Validator` en tu código
```php
<?php

$container->get('validation')->registerValidator(
    new Validator(
        'integer', //Nombre del validador
        'Must be integer type', //Mensaje de error
        function ($value):bool { 
            return (!is_array($value) && preg_match('/^\d+$/', "{$value}"));
        }
    )
);
```
ejemplo de validación en tu código
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
la respuesta al cliente se ve así:
```
Sent headers:
HTTP/1.1 400 Client Error: Bad Request
Content-Type: application/json
```
y la respuesta JSON
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

## Interfaz de línea de comandos
Crea el comando de ejemplo `app/commands/ExampleCommand.php` e implementa `app\services\CommandInterface`
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

    //Texto de ayuda para el comando --help
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
registra el comando en la configuración
```php
array(
    ...
    'commands' => array(
        'app\commands\ExampleCommand',
    ),
    ...
);
```
y llama al comando en la terminal
```shell script
# php index.php --command example --param1 Hello --param2 world
Hello world!
```
