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

namespace app\controllers;

use app\services\ContainerInterface;
use app\services\RequestInterface;
use app\services\ResponseInterface;
use app\models\Model;

/**
 * Class Controller
 * @package app\controllers
 */
abstract class Controller
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Controller constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->request = $container->get('request');
        $this->response = $container->get('response');
        $this->container = $container;
    }

    /**
     * @param string $model
     * @return Model
     */
    protected function getModel(string $model): Model
    {
        if (!class_exists($model)) {
            die(sprintf('Model "%s" not found', $model));
        }
        return new $model($this->container);
    }

    /**
     * Action before
     */
    public function onRequest() {}

    /**
     * @return string
     */
    abstract public function get(): string;

    /**
     * @return string
     */
    abstract public function post(): string;

    /**
     * @return string
     */
    abstract public function put(): string;

    /**
     * @return string
     */
    abstract public function delete(): string;

    /**
     * Action after
     */
    public function onResponse() {}

}