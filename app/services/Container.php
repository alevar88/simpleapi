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

/**
 * Class Container
 * @package app\services
 */
class Container implements ContainerInterface
{

    /**
     * @var ContainerInterface
     */
    private static $instance;

    /**
     * @var array
     */
    private $container = array();

    /**
     * Closed by Singleton
     */
    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return void
     */
    public function add(string $name, callable $callable)
    {
        $this->container[$name] = $callable;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return (isset($this->container[$name]));
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name)
    {
        if ($this->has($name) && is_callable($this->container[$name])) {
            $this->container[$name] = call_user_func($this->container[$name], $this);
        }
        return $this->container[$name] ?? null;
    }

}