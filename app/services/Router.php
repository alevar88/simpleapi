<?php
/**
 * This file is part of the "Simple RESTful-API PHP skeleton"
 *
 * @author Alexander Varnikov <alevar88@gmail.com>
 *
 * Project home: https://github.com/alevar88/simpleapi
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @copyright Copyright 2019, Alexander Varnikov <alevar88@gmail.com>, Alexander Groza <ametiloray@gmail.com>
 *
 */

declare(strict_types = 1);

namespace app\services;

use app\controllers\Controller;

/**
 * Class Router
 * @package app\services
 */
class Router implements RouterInterface
{

    /**
     * Supports HTTP methods
     */
    const ALLOWED_HTTP_METHODS = array(
        'GET', 'POST', 'PUT', 'DELETE'
    );

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var ValidationInterface
     */
    private $validation;

    /**
     * @var array
     */
    private $beforeRun = array();

    /**
     * @var array
     */
    private $routes = array();

    /**
     * Escape the path before method
     * <p>/api/users - here /api is escaped path</p>
     *
     * @var string
     */
    private $escPath = '';

    /**
     * Router constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->routes = $container->get('config')->router['routes'];
        $this->escPath = $container->get('config')->router['escPath'];
        $this->request = $container->get('request');
        $this->response = $container->get('response');
        $this->validation = $container->get('validation');
        $this->container = $container;
    }

    /**
     * @param callable $function
     * @return RouterInterface
     */
    public function beforeRun(callable $function): RouterInterface
    {
        $this->beforeRun[] = $function;
        return $this;
    }

    /**
     * @param callable $function
     * @return RouterInterface
     */
    public function onShutdown(callable $function): RouterInterface
    {
        register_shutdown_function(function () use ($function) {
            call_user_func($function, $this->container);
        });
        return $this;
    }

    /**
     * Run router dispatcher
     *
     * @throws ResponseException
     */
    public function run()
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPath();
        if (!empty($this->escPath)) {
            $path = str_replace($this->escPath, '', $path);
        }
        $this->request->setApiName($path);
        foreach ($this->beforeRun as $function) {
            call_user_func($function, $this->container);
        }
        if (!in_array($method, self::ALLOWED_HTTP_METHODS)) {
            throw new ResponseException(Response::E_HTTP_METHOD_NOT_ALLOWED, 405);
        }
        if ($this->container->has('authorization')) {
            if (!$this->container->get('authorization')->isAuth()) {
                throw new ResponseException(Response::E_BAD_TOKEN, 401);
            }
        }
        if (!$this->container->get('config')->allowEmptyRequests) {
            if (empty($this->request->getParams())) {
                throw new ResponseException(Response::E_EMPTY_REQUEST_BODY, 400);
            }
        }
        if (isset($this->routes[$path])) {
            $handler = $this->routes[$path]['handler'];
            if (class_exists($handler)) {
                //Validation
                $rules = $this->routes[$path]['rules'] ?? array();
                if ($this->validation->withRules($rules)->validate()) {
                    /** @var Controller $controller */
                    $controller = new $handler($this->container);
                    $controller->onRequest();
                    $response = call_user_func(array($controller, strtolower($method)));
                    $controller->onResponse();
                    exit($response);
                }
                //Failed validation, send errors
                $this->response->sendFailedValidate();
            }
            throw new ResponseException(Response::E_CONTROLLER_NOT_FOUND, 501);
        }
        throw new ResponseException(Response::E_API_METHOD_NOT_FOUND, 501);
    }

}