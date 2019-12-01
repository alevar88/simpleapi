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
 * Class Config
 * @package app\services
 */
class Config implements ConfigInterface
{

    /**
     * @var array
     */
    private $config = array();

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        return (isset($this->config[$name]));
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->config[$name] ?? null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->has($name);
    }

}