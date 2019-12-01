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
 * Interface LoggerInterface
 * @package app\services
 */
interface LoggerInterface
{

    /**
     * LoggerInterface constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @param string $name
     * @return void
     */
    public function setLogName(string $name);

    /**
     * @param string $message
     * @return bool
     */
    public function info(string $message): bool;

    /**
     * @param string $message
     * @return bool
     */
    public function warning(string $message): bool;

    /**
     * @param string $message
     * @return bool
     */
    public function error(string $message): bool;

    /**
     * @param string $message
     * @return bool
     */
    public function debug(string $message): bool;

}