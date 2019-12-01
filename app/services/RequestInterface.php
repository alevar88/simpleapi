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
 * Interface RequestInterface
 * @package app\services
 */
interface RequestInterface
{

    /**
     * RequestInterface constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value);

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool;

    /**
     * @return bool
     */
    public function isEmptyRequest(): bool;

    /**
     * @return string
     */
    public function getRequestId(): string;

    /**
     * @param $name
     * @return bool
     */
    public function has($name): bool;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param array $keys
     * @param array $filters
     * @return array
     */
    public function getParams(array $keys = array(), array $filters = array()): array;

    /**
     * @param string $name
     * @param int $filter
     * @return mixed|null
     */
    public function getParam(string $name, int $filter = FILTER_DEFAULT);

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @param string $name
     * @return void
     */
    public function setApiName(string $name);

    /**
     * @return string
     */
    public function getApiName(): string;

}