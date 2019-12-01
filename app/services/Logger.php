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
 * Class Logger
 * @package app\services
 */
class Logger implements LoggerInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $name;

    /**
     * Logger constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->dir = $container->get('config')->logger['dir'];
        $this->name = $container->get('config')->logger['name'];
        $this->container = $container;
    }

    /**
     * @param string $name
     */
    public function setLogName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $message
     * @param string $type
     * @return bool
     */
    private function toLog(string $message, string $type): bool
    {
        $format = '%s [%s] %s | %s' . PHP_EOL;
        $this->dir = $_SERVER['DOCUMENT_ROOT'] . $this->dir;
        if (file_exists($this->dir) && is_writable($this->dir)) {
            $message = sprintf($format, date('d.m.Y H:i:s'), $type, $_SERVER['REMOTE_ADDR'], $message);
            $result = (file_put_contents($this->dir . $this->name, $message, FILE_APPEND) > 0);
        }
        return $result ?? false;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function info(string $message): bool
    {
        return $this->toLog($message, 'INFO');
    }

    /**
     * @param string $message
     * @return bool
     */
    public function warning(string $message): bool
    {
        return $this->toLog($message, 'WARNING');
    }

    /**
     * @param string $message
     * @return bool
     */
    public function error(string $message): bool
    {
        return $this->toLog($message, 'ERROR');
    }

    /**
     * @param string $message
     * @return bool
     */
    public function debug(string $message): bool
    {
        return $this->toLog($message, 'DEBUG');
    }

}