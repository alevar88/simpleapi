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
 * Interface ContainerInterface
 * @package app\services
 */
interface ContainerInterface
{

    /**
     * @return static
     */
    public static function getContainer(): ContainerInterface;

    /**
     * @param string $name
     * @param callable $callable
     * @return void
     */
    public function add(string $name, callable $callable);

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

}