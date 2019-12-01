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

namespace app;

use app\services\AuthorizationInterface;
use app\services\Config;
use app\services\Console;
use app\services\Container;
use app\services\ContainerInterface;
use app\services\Database;
use app\services\Logger;
use app\services\Request;
use app\services\Response;
use app\services\Router;
use app\services\RouterInterface;
use app\services\Validation;
use app\services\Validator;

/**
 * Class Bootstrap
 * @package app
 */
class Bootstrap
{

    /**
     * @param array $config
     * @return RouterInterface
     */
    public static function load(array $config): RouterInterface
    {
        $container = Container::getContainer();
        $container->add('config', function (ContainerInterface $container) use ($config) {
            return new Config($config);
        });
        $container->add('request', function (ContainerInterface $container) {
            return new Request($container);
        });
        $container->add('response', function (ContainerInterface $container) {
            return new Response($container);
        });
        $container->add('validation', function (ContainerInterface $container) {
            $validation = new Validation($container);
            foreach ($container->get('config')->validators as list($name, $message, $callable)) {
                $validation->registerValidator(new Validator($name, $message, $callable));
            }
            return $validation;
        });
        $container->add('database', function (ContainerInterface $container) {
            return new Database($container->get('config')->database);
        });
        if ($container->get('config')->authorization['enable']) {
            $container->add('authorization', function (ContainerInterface $container) {
                $handler = $container->get('config')->authorization['handler'];
                if (class_exists($handler)) {
                    $handler = new $handler($container);
                    if (!$handler instanceof AuthorizationInterface) {
                        die(sprintf('Authorization handler "%s" is not implement AuthorizationInterface', get_class($handler)));
                    }
                    return $handler;
                }
                die(sprintf('Authorization Handler "%s" not found', $handler));
            });
        }
        $container->add('logger', function (ContainerInterface $container) {
            return new Logger($container);
        });
        $container->add('console', function (ContainerInterface $container) {
            return new Console($container);
        });
        if (PHP_SAPI === 'cli') {
            $container->get('console')->dispatch();
        }
        return new Router($container);
    }

}